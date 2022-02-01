<?php

namespace App\Controller;

use App\Entity\Category;
use App\Lib\GridGenerator;
use App\Lib\SettingsHandler;
use App\Lib\TreeGenerator;
use App\Repository\CategoryGroupRepository;
use App\Repository\CategoryRepository;
use App\Repository\SubAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TreeController extends AbstractController
{
    /**
     * @Route("/tree-last-six-months", name="tree_last_six_months_index")
     *
     * @return Response The rendered template
     */
    public function treeLastSixMonthsAction(): Response
    {
        return $this->render('default/treeLastSixMonths.html.twig');
    }

    /**
     * @Route("/tree", name="tree_index")
     *
     * @return Response The rendered template
     */
    public function treeAction(): Response
    {
        return $this->render('default/tree.html.twig');
    }

    /**
     * @Route("/tree/get-data-last-half-year", name="get_tree_data_last_half_year")
     *
     * @param TreeGenerator   $treeGenerator
     * @param SettingsHandler $settingsHandler
     *
     * @return JsonResponse The prepared tree json
     */
    public function getTreeDataLastHalfYearAction(TreeGenerator $treeGenerator, SettingsHandler $settingsHandler): JsonResponse
    {
        try {
            $period = $this->getPeriodLastHalfYear();

            $data = $treeGenerator->getTreeData($period, $settingsHandler->getMainAccount());

            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @Route("/tree/get-columns-last-half-year", name="get_columns_last_half_year")
     *
     * @return JsonResponse
     */
    public function getColNamesLastHalfYearAction(): JsonResponse
    {
        try {
            $period = $this->getPeriodLastHalfYear();

            $data = [];
            foreach ($period as $date) {
                $data[] = $date->format('F');
            }

            return new JsonResponse([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @Route("/tree/get-data-full-year", name="get_tree_data_full_year")
     *
     * @param Request         $request         The request
     * @param TreeGenerator   $treeGenerator   The tree helper
     * @param SettingsHandler $settingsHandler
     *
     * @return JsonResponse The prepared tree json
     */
    public function getTreeDataFullYearAction(Request $request, TreeGenerator $treeGenerator, SettingsHandler $settingsHandler): JsonResponse
    {
        try {
            $ts = new \DateTime();
            $year = $request->query->getInt('year', (int) $ts->format('Y'));
            $period = $this->getPeriodFullYear($year);

            $data = $treeGenerator->getTreeData($period, $settingsHandler->getMainAccount());

            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @Route("/tree/get-columns-full-year", name="get_columns_full_year")
     *
     * @return JsonResponse
     */
    public function getColNamesFullYearAction(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => array_values(array_reduce(range(1,12),function($rslt,$m){ $rslt[$m] = date('F',mktime(0,0,0,$m,10)); return $rslt; })),
        ]);
    }

    /**
     * @Route("/tree/get-detail-transactions", name="tree_get_detail_transactions")
     *
     * @param Request $request
     * @param GridGenerator $gridGenerator
     * @param CategoryGroupRepository $groupRepository
     * @param CategoryRepository $categoryRepository
     * @param SubAccountRepository $subAccountRepository
     * @param SettingsHandler $settingsHandler
     *
     * @return JsonResponse
     */
    public function getDetailTransactionsAction(Request $request, GridGenerator $gridGenerator, CategoryGroupRepository $groupRepository, CategoryRepository $categoryRepository, SubAccountRepository $subAccountRepository, SettingsHandler $settingsHandler): JsonResponse
    {
        try {
            $subAccount = $subAccountRepository->findOneBy(['id' => $settingsHandler->getMainAccount()]);
            $src = $gridGenerator->getGridData($subAccount, $request->query->getInt('year'), $request->query->getInt('month'));
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        $group = $groupRepository->findOneBy(['name' => $request->query->get('category')]);
        $ids = [];
        if ($group) {
            $ids = array_map(function(Category $category) {
                return $category->getId();
            }, $group->getCategories()->toArray());
        } else {
            $category = $categoryRepository->findOneBy(['name' => $request->query->get('category')]);
            if ($category) {
                $ids = [$category->getId()];
            }
        }

        $data = array_filter($src, function ($item) use ($ids) {
            return in_array(intval($item['category_id']), $ids);
        });

        return new JsonResponse(['success' => true, 'data' => array_values($data)]);
    }

    /**
     * @return \DatePeriod
     */
    private function getPeriodLastHalfYear(): \DatePeriod
    {
        $start = new \DateTime();
        $stop = clone $start;
        $start->modify('- 5 months');
        $start->modify('first day of');
        $start->setTime(0, 0, 0);
        $stop->modify('last day of');
        $stop->setTime(23, 59, 59);
        $interval = new \DateInterval('P1M');

        return new \DatePeriod($start, $interval, $stop);
    }

    /**
     * @param int $year
     *
     * @return \DatePeriod
     */
    private function getPeriodFullYear(int $year): \DatePeriod
    {
        $start = new \DateTime();
        $stop = clone $start;
        $start->setDate($year, 1, 1);
        $start->setTime(0, 0, 0);
        $stop->setDate($year, 12, 31);
        $stop->setTime(23, 59, 59);
        $interval = new \DateInterval('P1M');

        return new \DatePeriod($start, $interval, $stop);
    }
}
