<?php

namespace App\Controller;

use App\Lib\GridGenerator;
use App\Lib\SettingsHandler;
use App\Repository\SubAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secondary-controller")
 */
class SecondaryAccountController extends AbstractController
{
    /**
     * @Route("/index", name="secondary_controller_index")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('secondary_controller/index.html.twig');
    }

    /**
     * @Route("/get-data", name="secondary_controller_data")
     *
     * @param Request              $request              The request
     * @param GridGenerator        $generator            The grid generator object
     * @param SubAccountRepository $subAccountRepository
     *
     * @return JsonResponse
     */
    public function getDataAction(Request $request, GridGenerator $generator, SubAccountRepository $subAccountRepository): JsonResponse
    {
        $subAccount = $subAccountRepository->findOneBy(['id' => $request->query->getInt('subAccountId', 0)]);
        $year = $request->query->getInt('year', intval(date('Y')));

        return new JsonResponse([
            'success' => true,
            'data' => $generator->getGridData($subAccount, $year),
        ]);
    }

    /**
     * @Route("/get-subaccounts", name="secondary_controller_get_subaccounts")
     *
     * @param SettingsHandler      $settingsHandler
     * @param SubAccountRepository $subAccountRepository
     *
     * @return JsonResponse
     */
    public function getSubAccountsAction(SettingsHandler $settingsHandler, SubAccountRepository $subAccountRepository): JsonResponse
    {
        $mainSubAccountId = $settingsHandler->getMainAccount();

        $subAccounts = [];
        foreach ($subAccountRepository->findAll() as $subAccount) {
            if ($subAccount->getId() !== $mainSubAccountId) {
                $subAccounts[] = [
                    'id' => $subAccount->getId(),
                    'name' => $subAccount->getDescription().' '.$subAccount->getAccountNumber(),
                ];
            }
        }

        return new JsonResponse([
            'success' => true,
            'data' => $subAccounts,
        ]);
    }
}
