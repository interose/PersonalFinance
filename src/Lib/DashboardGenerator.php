<?php

namespace App\Lib;

use App\Entity\SubAccount;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class DashboardGenerator
{
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private TransactionRepository $transactionRepository;
    private MyDateTime $myDateTime;

    const NUMBER_OF_DAYS = 31;

    /**
     * DashboardGenerator constructor.
     *
     * @param EntityManagerInterface $em
     * @param LoggerInterface        $logger
     * @param TransactionRepository  $transactionRepository
     * @param MyDateTime             $myDateTime
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, TransactionRepository $transactionRepository, MyDateTime $myDateTime)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->transactionRepository = $transactionRepository;
        $this->myDateTime = $myDateTime;
    }

    /**
     * Calculate the savings rate according to the categories which are saved within the settings table
     *
     * @return int
     */
    public function getSavingsRate(): int
    {
        return $this->calcRateBySettingsCategory(SettingsHandler::SETTING_SAVINGS_CATEGORIES);
    }

    /**
     * Calculate the luxury rate according to the categories which are saved within the settings table
     *
     * @return int
     */
    public function getLuxuryRate(): int
    {
        return $this->calcRateBySettingsCategory(SettingsHandler::SETTING_LUXURY_CATEGORIES);
    }

    /**
     * @param SubAccount $subAccount
     *
     * @return array
     */
    public function getMainAccountProgressSeries(SubAccount $subAccount): array
    {
        $balance = (int) ($subAccount->getCurrentBalance()->getBalance() * 100);

        $start = $this->myDateTime->getToday();
        $start->setTimezone(new \DateTimeZone('Europe/Berlin'));
        $start->modify('first day of last month');
        $stop = $this->myDateTime->getToday();
        $stop->setTimezone(new \DateTimeZone('Europe/Berlin'));
        $stop->modify('last day of this month');

        $transactions = $this->transactionRepository->getSimple($subAccount, $start, $stop);

        $combined = $this->combineTransactions($transactions, $balance);

        list($seriesCurrentMonth, $seriesPreviousMonth) = $this->generateSeries($combined);

        $categories = array_map(function ($el) {
            return sprintf('%02d.', $el);
        }, range(1, self::NUMBER_OF_DAYS));

        return [$categories, $seriesCurrentMonth, $seriesPreviousMonth];
    }

    /**
     * @param SubAccount $subAccount
     *
     * @return array
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getMonthlyRemainings(SubAccount $subAccount): array
    {
        $start = $this->myDateTime->getToday();
        $start->setTime(0, 0, 0);
        $start->modify('- 13 months');
        $stop = $this->myDateTime->getToday();
        $stop->setTime(23, 59, 59);

        $data = $this->transactionRepository->getTransactionsForMonthlyRemainingDashboard($subAccount->getId(), $start, $stop);

        $chartData = $categories = [];

        // transform it
        array_walk($data, function ($item) use (&$chartData, &$categories) {
            $categories[] = $item['month_name'];
            $chartData[] =  $item['amount'] / 100;
        });

        return [$chartData, $categories];
    }

    /**
     * @param SubAccount $subAccount
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLastMonthGroupedSpendings(SubAccount $subAccount): array
    {
        $start = $this->myDateTime->getToday();
        $start->setTime(0, 0, 0);
        $start->modify('first day of last month');
        $stop = $this->myDateTime->getToday();
        $stop->setTime(23, 59, 59);
        $stop->modify('last day of last month');

        $src = $this->transactionRepository->getTransactionsForTreeView($subAccount->getId(), $start, $stop);
        $data = [];

        foreach ($src as $item) {
            $amount = floatval($item['amount'] ?? 0);
            $group = $item['category_group'] ?? '';
            if (strlen($group) === 0 || is_null($group)) {
                $group = $item['category'] ?? '';
            }

            if ($amount > 0) {
                continue;
            }

            if (!isset($data[$group])) {
                $data[$group] = $amount;
            } else {
                $data[$group] += $amount;
            }
        }

        return $data;
    }

    /**
     * @param string $settingsCategoryName
     *
     * @return int
     */
    private function calcRateBySettingsCategory(string $settingsCategoryName): int
    {
        $salary = $this->getSumBySettingsCategoryName(SettingsHandler::SETTING_SALARY_CATEGORIES);
        $settingsCategoryValue = $this->getSumBySettingsCategoryName($settingsCategoryName);

        return 0 === $salary ? 0 : round(($settingsCategoryValue / $salary) * 100);
    }

    /**
     * @param string $settingsCategoryName
     *
     * @return int
     */
    private function getSumBySettingsCategoryName(string $settingsCategoryName): int
    {
        $today = $this->myDateTime->getToday();
        $currentYear = $today->format('Y');

        $sql = <<<SQL
SELECT SUM(amount)
FROM transaction
WHERE category_id = (SELECT value FROM settings WHERE settings.name = '$settingsCategoryName') AND DATE_FORMAT(booking_date, '%Y') = $currentYear
SQL;

        return intval($this->execSimpleSql($sql));
    }

    /**
     * @param string $sql
     *
     * @return int
     */
    private function execSimpleSql(string $sql): int
    {
        try {
            $connection = $this->em->getConnection();
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();
            $data = $result->fetchFirstColumn();

            return $data[0] ?? 0;
        } catch (\Doctrine\DBAL\Exception $e) {
            $this->logger->error(sprintf('DashboardGenerator Exception: %s', $e->getMessage()));
        }

        return 0;
    }

    /**
     * This function creates from a list of transactions a series of account balance values based on days. It
     * aggregates all transactions within a day and adds/subtracts them to the balance from the previous day.
     * At the end, there is a backwards calculated list of the account balance for each day, beginning from the
     * current day.
     *
     * @param Transaction[] $data
     * @param int           $balance
     *
     * @return array
     */
    private function combineTransactions(array $data, int $balance): array
    {
        $result = [];

        // set the today's balance as an starting point, will be overwritten if there are
        // any transactions today
        $arrayKey = $this->myDateTime->getToday()->format('md');
        $result[$arrayKey] = $balance;

        array_walk($data, function ($item) use (&$result, &$balance) {
            $amount = intval($item->getAmount() * 100);
            $arrayKey = $item->getBookingDate()->format('md');

            if ($item->getCreditDebit() === 'debit') {
                $balance += $amount;
            } else {
                $balance -= $amount;
            }

            $result[$arrayKey] = $balance;
        });

        return $result;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function generateSeries(array $data): array
    {
        $seriesPreviousMonth = $seriesCurrentMonth = [];

        $ts = $this->myDateTime->getToday();
        $currentMonth = $ts->format('m');
        $currentDay = $ts->format('d');
        $ts->modify('first day of last month');
        $previousMonth = $ts->format('m');

        // get the first as a starting point
        $balance = $data[array_key_first($data)];

        for ($i = self::NUMBER_OF_DAYS; $i >= 1; $i--) {
            if ($i > $currentDay) {
                continue;
            }

            $combinedArrayKey = sprintf('%02d%02d', $currentMonth, $i);

            if (isset($data[$combinedArrayKey])) {
                $balance = $data[$combinedArrayKey];
            }

            // we consider the transactions of today in the past, so instead pushing them at the end we have to
            // put them at the beginning
            array_unshift($seriesCurrentMonth, round($balance / 100, 2));
        }

        for ($i = self::NUMBER_OF_DAYS; $i >= 1; $i--) {
            $combinedArrayKey = sprintf('%02d%02d', $previousMonth, $i);

            if (isset($data[$combinedArrayKey])) {
                $balance = $data[$combinedArrayKey];
            }

            // we consider the transactions of today in the past, so instead pushing them at the end we have to
            // put them at the beginning
            array_unshift($seriesPreviousMonth, round($balance / 100, 2));
        }

        return [$seriesCurrentMonth, $seriesPreviousMonth];
    }
}
