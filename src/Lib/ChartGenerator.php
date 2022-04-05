<?php

namespace App\Lib;

use App\Entity\Transaction;
use App\Repository\CategoryGroupRepository;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;

class ChartGenerator
{
    private TransactionRepository $transactionRepository;
    private CategoryGroupRepository $categoryGroupRepository;
    private CategoryRepository $categoryRepository;

    /**
     * ChartGenerator constructor.
     *
     * @param TransactionRepository $transactionRepository
     * @param CategoryGroupRepository $categoryGroupRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(TransactionRepository $transactionRepository, CategoryGroupRepository $categoryGroupRepository, CategoryRepository $categoryRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->categoryGroupRepository = $categoryGroupRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param int $accountId
     * @param int $categoryGroupId
     * @param bool $splitIntoCategories
     *
     * @return array
     *
     * @throws \Exception
     */
    public function generateChartSeries(int $accountId, int $categoryGroupId = 0, bool $splitIntoCategories = false): array
    {
        if (0 === $categoryGroupId) {
            return [[], []];
        }

        $categoryIds = [];
        if ($splitIntoCategories) {
            $categoryIds = array_keys($this->categoryRepository->getCategoriesByGroupId($categoryGroupId));
            $categoryGroupId = 0;
        }

        $series = $this->fetchAndCombine($accountId, $categoryGroupId, $categoryIds);

        $labels = array_keys($series);

        $this->fillGaps($series, $categoryGroupId, $categoryIds);

        $series = $this->split($series);
        $series = $this->addNames($series, $categoryGroupId);

        return [$labels, $series];
    }

    /**
     * @param int   $accountId
     * @param int   $categoryGroupId
     * @param array $categoryIds
     *
     * @return array
     *
     * @throws \Exception
     */
    private function fetchAndCombine(int $accountId, int $categoryGroupId = 0, array $categoryIds = []): array
    {
        $series = [];

        $fetchMethod = 'getTransactionsForYearChart';

        if (0 !== $categoryGroupId) {
            $fetchMethod .= 'ByCategoryGroup';
            $data = call_user_func_array([$this->transactionRepository, $fetchMethod], [$accountId, $categoryGroupId]);
            $this->combineData($data, $categoryGroupId, $series);
        } else {
            $fetchMethod .= 'ByCategory';
            foreach ($categoryIds as $categoryId) {
                $data = call_user_func_array([$this->transactionRepository, $fetchMethod], [$accountId, $categoryId]);
                $this->combineData($data, $categoryId, $series);
            }
        }

        return $series;
    }

    /**
     * @param array $data
     * @param int $id
     * @param array $series
     *
     * @return void
     */
    private function combineData(array $data, int $id, array &$series)
    {
        foreach ($data as $dataPoint) {
            $label = $dataPoint['year_number'];

            $series[$label][$id] = abs(($dataPoint['amount'] ?? 0 )/ 100);
        }
    }

    /**
     * @param array $series
     * @param int $categoryGroupId
     * @param array $categoryIds
     */
    private function fillGaps(array &$series, int $categoryGroupId = 0, array $categoryIds = [])
    {
        if (0 === $categoryGroupId) {
            $ids = $categoryIds;
        } else {
            $ids = [$categoryGroupId];
        }

        foreach ($series as $groupingName => $item) {
            $keys = array_keys($item);

            foreach ($ids as $id) {
                if (!in_array($id, $keys)) {
                    $series[$groupingName][$id] = .0;
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
            foreach ($data as $id => $amount) {
                $series[$id][] = $amount;
            }
        }

        return $series;
    }

    /**
     * @param array $src
     * @param int $categoryGroupId
     * @return array
     */
    private function addNames(array $src, int $categoryGroupId = 0): array
    {
        $series = [];

        if (0 === $categoryGroupId) {
            $repository = $this->categoryRepository;
        } else {
            $repository = $this->categoryGroupRepository;
        }

        foreach ($src as $id => $data) {
            $entity = $repository->findOneBy(['id' => $id]);

            $series[] = [
                'name' => $entity->getName(),
                'id' => $id,
                'data' => $data,
            ];
        }

        return $series;
    }
}
