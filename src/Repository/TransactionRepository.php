<?php

namespace App\Repository;

use App\Entity\SubAccount;
use App\Entity\Transaction;
use App\Lib\TransactionSqlHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    private TransactionSqlHelper $sqlHelper;

    /**
     * TransactionRepository constructor.
     *
     * @param ManagerRegistry      $registry  references Doctrine connections and entity managers
     * @param TransactionSqlHelper $sqlHelper
     */
    public function __construct(ManagerRegistry $registry, TransactionSqlHelper $sqlHelper)
    {
        $this->sqlHelper = $sqlHelper;

        parent::__construct($registry, Transaction::class);
    }

    /**
     * Fetch method for the chart feature.
     * Transactions are grouped by year and filtered by one category
     *
     * @param int $subAccountId
     * @param int $categoryGroupId
     *
     * @return array<int,array<string,mixed>>
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getTransactionsForYearChartByCategoryGroup(int $subAccountId, int $categoryGroupId): array
    {
        $sql = $this->sqlHelper->getSqlForYearChartByCategoryGroup();
        $params = [
            'subaccountid' => $subAccountId,
            'category_group_id' => $categoryGroupId,
        ];

        return $this->executeNativeSqlStatement($sql, $params);
    }

    /**
     * Fetch method for the chart feature.
     * Transactions are grouped by year and filtered by one category
     *
     * @param int $subAccountId
     * @param int $categoryId
     *
     * @return array<int,array<string,mixed>>
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getTransactionsForYearChartByCategory(int $subAccountId, int $categoryId): array
    {
        $sql = $this->sqlHelper->getSqlForYearChartByCategory();
        $params = [
            'subaccountid' => $subAccountId,
            'category_id' => $categoryId,
        ];

        return $this->executeNativeSqlStatement($sql, $params);
    }

//    /**
//     * Fetch method for the chart feature.
//     * Transactions are grouped by month and filtered by one category
//     *
//     * @param int $subAccountId
//     * @param int $categoryGroup
//     * @param int $category
//     *
//     * @return array<int,array<string,mixed>>
//     *
//     * @throws Exception
//     * @throws \Doctrine\DBAL\Driver\Exception
//     */
//    public function getTransactionsForMonthChart(int $subAccountId, int $categoryGroup = 0, int $category = 0): array
//    {
//        $sql = $this->sqlHelper->getSqlForMonthChart();
//        $params = [
//            'subaccountid' => $subAccountId,
//            'category_group_id' => $categoryGroup,
//        ];
//
//        return $this->executeNativeSqlStatement($sql, $params);
//    }

    /**
     * @param int                $subAccountId
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $stop
     *
     * @return array<int,array<string,mixed>>
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getTransactionsForTreeView(int $subAccountId, \DateTimeInterface $start, \DateTimeInterface $stop): array
    {
        $sql = $this->sqlHelper->getSqlForForTreeView();

        $params = [
            'subaccountid' => $subAccountId,
            'start' => $start->format('Y-m-d'),
            'stop' => $stop->format('Y-m-d'),
        ];

        return $this->executeNativeSqlStatement($sql, $params);
    }

    /**
     * @param int                $subAccountId
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $stop
     *
     * @return array<int,array<string,mixed>>
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getTransactionsForMonthlyRemainingDashboard(int $subAccountId, \DateTimeInterface $start, \DateTimeInterface $stop): array
    {
        $sql = $this->sqlHelper->getSqlForMonthlyRemainingDashboard();

        $params = [
            'subaccountid' => $subAccountId,
            'start' => $start->format('Y-m-d'),
            'stop' => $stop->format('Y-m-d'),
        ];

        return $this->executeNativeSqlStatement($sql, $params);
    }

    /**
     * @param int                $subAccountId
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $stop
     *
     * @return array<int,array<string,mixed>>
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getTransactionsForMonthlyOverviewDashboard(int $subAccountId, \DateTimeInterface $start, \DateTimeInterface $stop): array
    {
        $sql = $this->sqlHelper->getSqlForForDashboardMonthOverview();

        $params = [
            'subaccountid' => $subAccountId,
            'start' => $start->format('Y-m-d'),
            'stop' => $stop->format('Y-m-d'),
        ];

        return $this->executeNativeSqlStatement($sql, $params);
    }

    /**
     * @param SubAccount         $subAccount
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $stop
     *
     * @return mixed
     */
    public function getSimple(SubAccount $subAccount, \DateTimeInterface $start, \DateTimeInterface $stop): mixed
    {
        $qb = $this->createQueryBuilder('t');

        $qb->andWhere('t.subAccount = :subAccount');
        $qb->setParameter('subAccount', $subAccount);

        $qb->andWhere('t.valutaDate >= :start');
        $qb->setParameter('start', $start->format('Y-m-d'));

        $qb->andWhere('t.valutaDate <= :end');
        $qb->setParameter('end', $stop->format('Y-m-d'));

        $qb->orderBy('t.valutaDate', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Fetch all data from the transaction table for a given subaccount and year. Additional the result
     * could be limited by a given month, only unassigned transactions, search for a name or search for a
     * description.
     * Split-transactions are joined, no grouping will be applied.
     *
     * @param SubAccount $subAccount     The subaccount for which the data should be fetched
     * @param int        $year           Filter for year
     * @param int        $month          Filter for month
     * @param bool       $onlyUnassigned Filter for not assigned transactions
     * @param string     $name           Filter for name
     * @param string     $description    Filter for description
     *
     * @return mixed The query result
     */
    public function getByYear(SubAccount $subAccount, int $year = 0, int $month = 0, bool $onlyUnassigned = false, string $name = '', string $description = ''): mixed
    {
        $qb = $this->createQueryBuilder('t');

        $qb->leftJoin('t.splitTransactions', 'st');
        $qb->addSelect('st');

        $qb->leftJoin('t.category', 'c');
        $qb->addSelect('c');

        $qb->andWhere('t.subAccount = :subAccount');
        $qb->setParameter('subAccount', $subAccount);

        $start = $end = null;

        if (0 !== $year && 0 !== $month) {
            $start = strtotime(sprintf('%d/01/%d 00:00:00', $month, $year));
            $tmp = strtotime('+1 month', $start);
            $end = strtotime('-1 second', $tmp);

        } else if (0 !== $year) {
            $start = strtotime(sprintf('01/01/%d 00:00:00', $year));
            $end = strtotime(sprintf('12/31/%d 00:00:00', $year));
        }

        if (null !== $start && null !== $end) {
            $qb->andWhere('t.valutaDate BETWEEN :start AND :end');
            $qb->setParameter('start', date('Y-m-d', $start));
            $qb->setParameter('end', date('Y-m-d', $end));
        }

        if ($onlyUnassigned) {
            $qb->andWhere('t.category IS NULL');
            $qb->andWhere('st.category IS NULL');
        }

        if (strlen($name) !== 0) {
            $qb->andWhere($qb->expr()->like('t.name', ':name'));
            $qb->setParameter('name', '%'.$name.'%');
        }

        if (strlen($description) !== 0) {
            $qb->andWhere($qb->expr()->like('t.description', ':description'));
            $qb->setParameter('description', '%'.$description.'%');
        }

        $qb->orderBy('t.valutaDate', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function getPayPalTransactions(\DateTimeInterface $payPalTransactionDate)
    {
        $con = $this->getEntityManager()->getConnection();
        $sql = <<<SQL
SELECT id, valuta_date, description, ROUND(amount / 100, 2) AS amount
FROM transaction
WHERE valuta_date BETWEEN DATE_SUB(:paypalDate, INTERVAL 5 DAY) AND DATE_ADD(:paypalDate, INTERVAL 5 DAY)
  AND name LIKE 'PayPal%'
  AND pay_pal_transaction_id IS NULL  
SQL;
        $stmt = $con->prepare($sql);
        $result = $stmt->executeQuery([
            'paypalDate' => $payPalTransactionDate->format('Y-m-d')
        ]);

        return $result->fetchAllAssociative();
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return array<int,array<string,mixed>>
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    private function executeNativeSqlStatement(string $sql, array $params): array
    {
        $con = $this->getEntityManager()->getConnection();
        $con->executeStatement('SET lc_time_names = \'de_DE\'');
        $stmt = $con->prepare($sql);
        $result = $stmt->executeQuery($params);

        return $result->fetchAllAssociative();
    }
}
