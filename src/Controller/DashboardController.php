<?php

namespace App\Controller;

use App\Lib\DashboardGenerator;
use App\Lib\SettingsHandler;
use App\Repository\SubAccountRepository;
use Doctrine\DBAL\Driver\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @param DashboardGenerator   $dashboardGenerator
     * @param SubAccountRepository $repository
     *
     * @return Response The rendered template
     */
    public function indexAction(DashboardGenerator $dashboardGenerator, SubAccountRepository $repository): Response
    {
        $subAccounts = $repository->findAll();

        return $this->render('default/dashboard.html.twig', [
            'savingsRate' => $dashboardGenerator->getSavingsRate(),
            'luxuryRate' => $dashboardGenerator->getLuxuryRate(),
            'subAccounts' => $subAccounts,
        ]);
    }

    /**
     * @Route("/dashboard/get-account-progress", name="dashboard_get_account_progress")
     *
     * @param SettingsHandler      $settingsHandler
     * @param SubAccountRepository $subAccountRepository
     * @param DashboardGenerator   $dashboardGenerator
     *
     * @return JsonResponse
     */
    public function getAccountProgressAction(SettingsHandler $settingsHandler, SubAccountRepository $subAccountRepository, DashboardGenerator $dashboardGenerator): JsonResponse
    {
        try {
            $subAccount = $subAccountRepository->findOneBy(['id' => $settingsHandler->getMainAccount()]);
            list($categories, $seriesCurrentMonth, $seriesPreviousMonth) = $dashboardGenerator->getMainAccountProgressSeries($subAccount);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'categories' => $categories,
            'seriesPreviousMonth' => $seriesPreviousMonth,
            'seriesCurrentMonth' => $seriesCurrentMonth,
        ]);
    }

    /**
     * @Route("/dashboard/get-monthly-remaining", name="dashboard_get_monthly_remaining")
     *
     * @param SettingsHandler      $settingsHandler
     * @param SubAccountRepository $subAccountRepository
     * @param DashboardGenerator   $dashboardGenerator
     *
     * @return JsonResponse The result as json response
     */
    public function getMonthlyRemainingAction(SettingsHandler $settingsHandler, SubAccountRepository $subAccountRepository, DashboardGenerator $dashboardGenerator): JsonResponse
    {
        try {
            $subAccount = $subAccountRepository->findOneBy(['id' => $settingsHandler->getMainAccount()]);
            list($chartData, $categories) = $dashboardGenerator->getMonthlyRemainings($subAccount);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'categories' => $categories,
            'data' => $chartData,
        ]);
    }

    /**
     * @Route("/dashboard/get-month-overview", name="dashboard_get_month_overview")
     *
     * @param SettingsHandler $settingsHandler
     * @param SubAccountRepository $subAccountRepository
     * @param DashboardGenerator $dashboardGenerator
     *
     * @return JsonResponse
     */
    public function getMonthOverview(SettingsHandler $settingsHandler, SubAccountRepository $subAccountRepository, DashboardGenerator $dashboardGenerator): JsonResponse
    {
        try {
            $subAccount = $subAccountRepository->findOneBy(['id' => $settingsHandler->getMainAccount()]);
            $dataLastMonth = $dashboardGenerator->getLastMonthGroupedSpendings($subAccount);
            $dataCurrentMonth = $dashboardGenerator->getCurrentMonthGroupedSpendings($subAccount);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'dataLastMonth' => $dataLastMonth,
            'dataCurrentMonth' => $dataCurrentMonth,
        ]);
    }
}
