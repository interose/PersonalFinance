<?php

namespace App\Lib;

use App\Entity\Transaction;
use App\Repository\CategoryGroupRepository;
use App\Repository\TransactionRepository;

class ChartGenerator
{
    private TransactionRepository $transactionRepository;
    private CategoryGroupRepository $categoryGroupRepository;

    /**
     * ChartGenerator constructor.
     *
     * @param TransactionRepository   $transactionRepository
     * @param CategoryGroupRepository $categoryGroupRepository
     */
    public function __construct(TransactionRepository $transactionRepository, CategoryGroupRepository $categoryGroupRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->categoryGroupRepository = $categoryGroupRepository;
    }

    /**
     * @param int   $accountId
     * @param array $categoryGroups
     * @param int   $grouping
     *
     * @return array
     *
     * @throws \Exception
     */
    public function generateChartSeries(int $accountId, array $categoryGroups, int $grouping = Transaction::GROUPING_YEARLY): array
    {
        if (Transaction::GROUPING_YEARLY !== $grouping && Transaction::GROUPING_MONTHLY !== $grouping) {
            throw new \Exception(sprintf('Unsupported grouping type: %d', $grouping));
        }

        $series = $this->fetchAndCombine($accountId, $categoryGroups, $grouping);

        $labels = array_keys($series);

        $this->fillGaps($series, $categoryGroups);

        $series = $this->split($series);
        $series = $this->addCategoryNames($series);

        return [$labels, $series];
    }

    /**
     * @param int   $accountId
     * @param array $categoryGroups
     * @param int   $grouping
     *
     * @return array
     *
     * @throws \Exception
     */
    private function fetchAndCombine(int $accountId, array $categoryGroups, int $grouping): array
    {
        $series = [];

        $fetchMethod = 'getTransactionsForMonthChart';
        if (Transaction::GROUPING_YEARLY === $grouping) {
            $fetchMethod = 'getTransactionsForYearChart';
        }

        foreach ($categoryGroups as $categoryGroupId) {
            $data = call_user_func_array([$this->transactionRepository, $fetchMethod], [$accountId, $categoryGroupId]);

            foreach ($data as $dataPoint) {
                if (Transaction::GROUPING_YEARLY === $grouping) {
                    $label = $dataPoint['year_number'];
                } else {
                    $label = $dataPoint['month_number'];
                }

                $series[$label][$categoryGroupId] = abs(($dataPoint['amount'] ?? 0 )/ 100);
            }
        }

        return $series;
    }

    /**
     * @param array $series
     * @param array $categoryGroups
     */
    private function fillGaps(array &$series, array $categoryGroups)
    {
        foreach ($series as $groupingName => $item) {
            $keys = array_keys($item);

            foreach ($categoryGroups as $categoryGroupId) {
                if (!in_array($categoryGroupId, $keys)) {
                    $series[$groupingName][$categoryGroupId] = .0;
                }
            }
        }
    }

    /**
     * @param array $src
     *
     * @return array
     */
    private function split(array $src): array
    {
        $series = [];

        foreach ($src as $data) {
            foreach ($data as $categoryName => $amount) {
                $series[$categoryName][] = $amount;
            }
        }

        return $series;
    }

    /**
     * @param array $src
     *
     * @return array
     */
    private function addCategoryNames(array $src): array
    {
        $series = [];

        foreach ($src as $categoryGroupId => $data) {
            $categoryGroupEntity = $this->categoryGroupRepository->findOneBy(['id' => $categoryGroupId]);

            $series[] = [
                'name' => $categoryGroupEntity->getName(),
                'id' => $categoryGroupEntity->getId(),
                'data' => $data,
            ];
        }

        return $series;
    }
}
