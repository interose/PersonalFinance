<?php

namespace App\Controller;

use App\Lib\GridGenerator;
use App\Lib\SettingsHandler;
use App\Repository\CategoryRepository;
use App\Repository\SubAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/grid")
 */
class GridController extends AbstractController
{
    /**
     * @Route("/", name="grid_index")
     *
     * @return Response The rendered template
     */
    public function gridAction(): Response
    {
        return $this->render('default/grid.html.twig');
    }

    /**
     * @Route("/get-data", name="get_grid_data")
     *
     * @param Request              $request              The request
     * @param GridGenerator        $generator            The grid generator object
     * @param SubAccountRepository $subAccountRepository
     * @param SettingsHandler      $settingsHandler
     *
     * @return JsonResponse
     */
    public function getGridDataAction(Request $request, GridGenerator $generator, SubAccountRepository $subAccountRepository, SettingsHandler $settingsHandler): JsonResponse
    {
        $subAccount = $subAccountRepository->findOneBy(['id' => $settingsHandler->getMainAccount()]);

        $onlyUnassigned = (bool) $request->query->get('onlyunassigned', 0);
        $name = $request->query->get('name', '');
        $description = $request->query->get('description', '');

        $year = $request->query->getInt('year', intval(date('Y')));
        $month = $request->query->getInt('month');

        return new JsonResponse([
            'success' => true,
            'data' => $generator->getGridData($subAccount, $year, $month, $onlyUnassigned, $name, $description),
        ]);
    }

    /**
     * @Route("/get-category-data", name="get_category_data")
     *
     * @param CategoryRepository $repository The category repository
     *
     * @return JsonResponse
     */
    public function getCategoryDataAction(CategoryRepository $repository): JsonResponse
    {
        $data = $repository->getAsArray();

        return new JsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }
}
