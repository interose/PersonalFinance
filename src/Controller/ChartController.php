<?php

namespace App\Controller;

use App\Form\ChartCategoryType;
use App\Lib\ChartGenerator;
use App\Lib\SettingsHandler;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chart")
 */
class ChartController extends AbstractController
{
    /**
     * @Route("/index", name="chart_index")
     *
     * @param CategoryRepository $repository
     *
     * @return Response The template
     */
    public function indexAction(CategoryRepository $repository): Response
    {
        $form = $this->createForm(ChartCategoryType::class, null, [
            'categoryRepository' => $repository,
        ]);

        return $this->render('chart/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/data", name="chart_data")
     *
     * @param Request         $request
     * @param ChartGenerator  $chartGenerator
     * @param SettingsHandler $settingsHandler
     *
     * @return JsonResponse
     */
    public function getChartDataAction(Request $request, ChartGenerator $chartGenerator, SettingsHandler $settingsHandler): JsonResponse
    {
        $grouping = $request->query->getInt('grouping');
        $categories = explode(',', $request->query->get('categories'));

        try {
            list($labels, $data) = $chartGenerator->generateChartSeries($settingsHandler->getMainAccount(), $categories, $grouping);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'data' => $data,
            'labels' => $labels,
        ]);
    }
}
