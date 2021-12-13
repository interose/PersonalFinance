<?php

namespace App\Controller;

use App\Lib\TransactionHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    /**
     * @Route("/update-category", name="update_category")
     *
     * @param Request            $request            The request
     * @param TransactionHandler $transactionHandler The transaction handler object
     *
     * @return JsonResponse
     */
    public function updateCategoryAction(Request $request, TransactionHandler $transactionHandler): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $categoryId = isset($content['category_id']) && $content['category_id'] > 0 ? $content['category_id'] : null;
        $transactionId = isset($content['id_transaction']) && $content['id_transaction'] > 0 ? $content['id_transaction'] : null;
        $splitTransactionId = isset($content['id_splittransaction']) && $content['id_splittransaction'] > 0 ? $content['id_splittransaction'] : null;

        try {
            $transactionHandler->update($categoryId, $transactionId, $splitTransactionId);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
