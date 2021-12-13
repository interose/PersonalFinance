<?php

namespace App\Lib;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Exception;

class TreeGenerator
{
    const CATEGORY_GROUP_UNDEFINED = 'undefined';

    private TransactionRepository $repository;

    /**
     * TreeGenerator constructor.
     *
     * @param TransactionRepository $repository The transaction repository
     */
    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Fetches the data from the database and creates a tree structure.
     *
     * @param \DatePeriod $period       The period for which the data should be fetched
     * @param int         $subAccountId The subaccount for which the transactions should be fetched
     *
     * @return array The formatted extjs tree
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getTreeData(\DatePeriod $period, int $subAccountId): array
    {
        $empty = $extJsKeys = [];

        $i = 1;
        /** @var \DateTime $date */
        foreach ($period as $date) {
            // We need an array with the empty month values for the given period in order to group the data
            $empty[$date->format('Y-m')] = 0;
            // And we need also an empty array with incrementing month values for the dataIndex value of the
            // extjs tree grid. The data index could not be adjusted dynamically to the requested date time period.
            // So for this reason we have to change the month array keys at the end.
            $extJsKeys[] = sprintf('month%02d', $i);
            ++$i;
        }

        $data = $this->fetchAndGroupData($period->getStartDate(), $period->getEndDate(), $subAccountId);
        $data = $this->buildExtJsTree($data, $empty, $extJsKeys);
        $data = $this->sortTree($data);

        return [
            'text' => '.',
            'expanded' => false,
            'children' => $data,
        ];
    }

    /**
     * This function fetches the data from the database and groups them based on category_group,
     * category and month.
     *
     * @param \DateTimeInterface $start
     * @param \DateTimeInterface $stop
     * @param int                $subAccountId The subaccount for which the transactions should be fetched
     *
     * @return array The grouped data
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    private function fetchAndGroupData(\DateTimeInterface $start, \DateTimeInterface  $stop, int $subAccountId): array
    {
        $result = [];
        // we have to group the data hierarchically
        // e.g. [category_group][category][2020-01] = amount
        $raw = $this->repository->getTransactionsForTreeView($subAccountId, $start, $stop);
        foreach ($raw as $item) {
            $group = !is_null($item['category_group']) ? $item['category_group'] : self::CATEGORY_GROUP_UNDEFINED;

            $result[$group][$item['category']][$item['month_number']] = $item['amount'];
        }

        return $result;
    }

    /**
     * This function reformat the grouped data ([category_group][category][2020-01] = amount) into a tree
     * structure, which will be needed for the extjs tree grid.
     * [category_group] = [
     *      [category] = [
     *          'month01' => 0
     *          'month02' => 0
     *          'month03' => 0
     *          'month04' => 0
     *          ...
     *      ]
     * ].
     *
     * @param array $data        The grouped data from the database
     * @param array $emptyMonths an an array with zero values and the year-month numbers as keys for each month within the requested period
     * @param array $extJsKeys   an array with zero values and a incrementing month value as key in order to map the dynamic month numbers to the extjs columns
     *
     * @return array The tree
     */
    private function buildExtJsTree(array $data, array $emptyMonths, array $extJsKeys): array
    {
        $result = [];

        $overallSums = [] + $emptyMonths;

        // now let's build the tree structure for the extjs tree component
        foreach ($data as $categoryGroup => $categories) {
            $childs = [];
            // create an group sum array with the default zero month values
            $groupSum = [] + $emptyMonths;
            foreach ($categories as $category => $values) {
                // create a single child tree line with the default zero month values
                $categorySums = $emptyMonths;

                // after that, replace the zeros with the values from the query
                // and sum up the values for the category group tree line
                foreach ($values as $month => $amount) {
                    $categorySums[$month] = (float) $amount;
                    $groupSum[$month] += (float) $amount;

                    $overallSums[$month] += (float) $amount;
                }

                // we have to delete the dynamic array index
                // it is not possible to create a tree with dynamic column data index
                $categorySums = array_combine($extJsKeys, array_values($categorySums));

                // add the category child line to the childs array in order to add all childs
                // of the group to the category group tree element
                $categorySums['name'] = $category;
                $categorySums['leaf'] = true;
                $categorySums['iconCls'] = 'tree-none';

                $childs[] = $categorySums;
            }

            usort($childs, function ($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });

            if (self::CATEGORY_GROUP_UNDEFINED === $categoryGroup) {
                // category has no parent group, so instead of adding them as childs, append them at the same
                // level as the category groups
                $result = array_merge($result, $childs);
            } else {
                // we have to delete the dynamic array index
                // it is not possible to create a tree with dynamic column data index
                $groupSum = array_combine($extJsKeys, array_values($groupSum));

                $groupSum['name'] = $categoryGroup;
                $groupSum['children'] = $childs;

                // we are done with the dependent categories of the group, so add the childs and the
                // summed up values
                $result[] = $groupSum;
            }
        }

        // we have to delete the dynamic array index
        // it is not possible to create a tree with dynamic column data index
        $overallSums = array_combine($extJsKeys, array_values($overallSums));
        $overallSums['name'] = 'Gesamt';
        $overallSums['leaf'] = true;
        $overallSums['iconCls'] = 'tree-none';

        $result[] = $overallSums;

        return $result;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function sortTree(array $data): array
    {
        $sumRecord = $data[count($data) -1];
        unset($data[count($data) -1]);

        usort($data, function ($a, $b) {
            $isLeafA = $a['leaf'] ?? false;
            $isLeafB = $b['leaf'] ?? false;

            if ($isLeafA === $isLeafB) {
                return strcasecmp($a['name'], $b['name']);
            }

            return $isLeafA < $isLeafB ? -1 : 1;
        });

        $data[] = $sumRecord;

        return $data;
    }
}
