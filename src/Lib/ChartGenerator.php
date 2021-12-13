<?php

namespace App\Lib;

use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;

class ChartGenerator
{
    private TransactionRepository $transactionRepository;
    private CategoryRepository $categoryRepository;

    /**
     * ChartGenerator constructor.
     *
     * @param TransactionRepository $transactionRepository
     * @param CategoryRepository    $categoryRepository
     */
    public function __construct(TransactionRepository $transactionRepository, CategoryRepository $categoryRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param int   $accountId
     * @param array $categories
     * @param int   $grouping
     *
     * @return array
     *
     * @throws \Exception
     */
    public function generateChartSeries(int $accountId, array $categories, int $grouping = Transaction::GROUPING_YEARLY): array
    {
        if (Transaction::GROUPING_YEARLY !== $grouping && Transaction::GROUPING_MONTHLY !== $grouping) {
            throw new \Exception(sprintf('Unsupported grouping type: %d', $grouping));
        }

        $series = $this->fetchAndCombine($accountId, $categories, $grouping);

        $labels = array_keys($series);

        $this->fillGaps($series, $categories);

        $series = $this->split($series);
        $series = $this->addCategoryNames($series);

        return [$labels, $series];
    }

    /**
     * @param int   $accountId
     * @param array $categories
     * @param int   $grouping
     *
     * @return array
     *
     * @throws \Exception
     */
    private function fetchAndCombine(int $accountId, array $categories, int $grouping): array
    {
        $series = [];

        $fetchMethod = 'getTransactionsForMonthChart';
        if (Transaction::GROUPING_YEARLY === $grouping) {
            $fetchMethod = 'getTransactionsForYearChart';
        }

        foreach ($categories as $categoryId) {
            $data = call_user_func_array([$this->transactionRepository, $fetchMethod], [$accountId, $categoryId]);

            foreach ($data as $dataPoint) {
                if (Transaction::GROUPING_YEARLY === $grouping) {
                    $label = $dataPoint['year_number'];
                } else {
                    $label = $dataPoint['month_number'];
                }

                $series[$label][$categoryId] = abs($dataPoint['amount'] / 100 ?? 0);
            }
        }

        return $series;
    }

    /**
     * @param array $series
     * @param array $categories
     */
    private function fillGaps(array &$series, array $categories)
    {
        foreach ($series as $groupingName => $item) {
            $keys = array_keys($item);

            foreach ($categories as $categoryId) {
                if (!in_array($categoryId, $keys)) {
                    $series[$groupingName][$categoryId] = .0;
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

        foreach ($src as $categoryId => $data) {
            $categoryEntity = $this->categoryRepository->findOneBy(['id' => $categoryId]);

            $series[] = [
                'name' => $categoryEntity->getName(),
                'id' => $categoryEntity->getId(),
                'data' => $data,
            ];
        }

        return $series;
    }
}
