<?php

namespace App\Controller;

use App\Lib\AccountUpdateHandler;
use App\Lib\TanRequiredException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/update", name="update")
     *
     * @param AccountUpdateHandler $accountUpdateHandler
     * @param Request              $request
     * @param TranslatorInterface  $translator
     *
     * @return JsonResponse
     */
    public function updateAccountsAction(AccountUpdateHandler $accountUpdateHandler, Request $request, TranslatorInterface $translator): JsonResponse
    {
        try {
            $response = $accountUpdateHandler->updateAll($request->request->get('tan'));
        } catch (TanRequiredException $e) {
            return new JsonResponse([
                'success' => false,
                'type' => 'tan',
                'modalTitle' => $translator->trans('Please enter TAN'),
                'modalBody' => $this->renderView('transfer/_tanForm.html.twig'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'type' => 'error',
                'modalTitle' => $translator->trans('An error has occurred!'),
                'message' => $e->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'modalTitle' => $translator->trans('Update complete!'),
            'data' => $response,
        ]);
    }
}
