<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\SplitTransaction;
use App\Entity\Transaction;
use App\Lib\SplitTransactionHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SplitTransactionController extends AbstractController
{
    /**
     * @Route("/split-transaction", name="split_transaction_post", methods={"POST"})
     *
     * @param Request                $request    The request
     * @param EntityManagerInterface $em         The entity manager
     * @param TranslatorInterface    $translator
     *
     * @return JsonResponse
     */
    public function createAction(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $splitTransaction = new SplitTransaction();

        $category = $em->getRepository(Category::class)->findOneBy(['id' => $data['category_id'] ?? 0]);
        if (!$category) {
            return new JsonResponse([
                'success' => false,
                'errors' => $translator->trans('Could not find category for given id (ID = %category_id%)', ['%category_id%' => $data['category_id'] ?? 0]),
            ]);
        }
        $splitTransaction->setCategory($category);

        $transaction = $em->getRepository(Transaction::class)->findOneBy(['id' => $data['transaction'] ?? 0]);
        if (!$transaction) {
            return new JsonResponse([
                'success' => false,
                'errors' => $translator->trans('Could not find transaction for given id (ID = %transaction_id%)', ['%transaction_id%' => $data['transaction'] ?? 0]),
            ]);
        }
        $splitTransaction->setTransaction($transaction);

        if (!isset($data['valuta_date']) || !isset($data['description']) || !isset($data['amount'])) {
            return new JsonResponse([
                'success' => false,
                'errors' => $translator->trans('Missing fields'),
            ]);
        }

        $valutaDate = \DateTime::createFromFormat('Y-m-d\TH:i:s', $data['valuta_date'] ?? 0);
        if (false === $valutaDate) {
            $valutaDate = new \DateTime();
        }
        $splitTransaction->setValutaDate($valutaDate);

        $splitTransaction->setDescription($data['description'] ?? '');
        $splitTransaction->setAmount($data['amount'] ?? 0);

        $em->persist($splitTransaction);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'data' => [
                'idSplitTransaction' => $splitTransaction->getId(),
                'transaction' => $splitTransaction->getTransaction()->getId(),
                'description' => $splitTransaction->getDescription(),
                'amount' => $splitTransaction->getAmount(),
                'category_name' => $splitTransaction->getCategory()->getName(),
                'category_id' => $splitTransaction->getCategory()->getId(),
                'valuta_date' => $splitTransaction->getValutaDate()->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * @Route("/split-transaction", name="split_transaction_update", methods={"PATCH"})
     *
     * @param Request                $request    The request
     * @param EntityManagerInterface $em         The entity manager
     * @param TranslatorInterface    $translator
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $splitTransaction = $em->getRepository(SplitTransaction::class)->findOneBy(['id' => $data['idSplitTransaction'] ?? 0]);
        if (!$splitTransaction) {
            return new JsonResponse([
                'success' => false,
                'errors' => $translator->trans('Could not find split transaction for given id (ID = %transaction_id%)', ['%transaction_id%' => $data['idSplitTransaction'] ?? 0]),
            ]);
        }

        $category = $em->getRepository(Category::class)->findOneBy(['id' => $data['category_id'] ?? 0]);
        if (!$category) {
            return new JsonResponse([
                'success' => false,
                'errors' => $translator->trans('Could not find category for given id (ID = %category_id%)', ['%category_id%' => $data['category_id'] ?? 0]),
            ]);
        }
        $splitTransaction->setCategory($category);

        $valutaDate = \DateTime::createFromFormat('Y-m-d\TH:i:s', $data['valuta_date'] ?? 0);
        if (false === $valutaDate) {
            $valutaDate = new \DateTime();
        }
        $splitTransaction->setValutaDate($valutaDate);

        if (isset($data['amount'])) {
            $splitTransaction->setAmount($data['amount']);
        }

        if (isset($data['description'])) {
            $splitTransaction->setDescription($data['description']);
        }

        $em->persist($splitTransaction);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'data' => [
                'idSplitTransaction' => $splitTransaction->getId(),
                'transaction' => $splitTransaction->getTransaction()->getId(),
                'description' => $splitTransaction->getDescription(),
                'amount' => $splitTransaction->getAmount(),
                'category_name' => $splitTransaction->getCategory()->getName(),
                'category_id' => $splitTransaction->getCategory()->getId(),
                'valuta_date' => $splitTransaction->getValutaDate()->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * @Route("/split-transaction", name="split_transaction_get", methods={"GET"})
     *
     * @param Request                 $request The request
     * @param SplitTransactionHandler $handler The lib for handling split transactions
     *
     * @return JsonResponse
     */
    public function readAction(Request $request, SplitTransactionHandler $handler): JsonResponse
    {
        try {
            $handler->setTransactionId($request->query->get('idTransaction'));
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        $data = $handler->getChildTransactions();

        // get sum of split amounts
        $sum = 0;
        foreach ($data as $item) {
            $sum += floatval($item['amount']);
        }

        $amount = floatval($handler->getTransaction()->getAmount()) - $sum;

        return new JsonResponse([
            'success' => true,
            'data' => $data,
            'transaction' => [
                'description' => $handler->getTransaction()->getDescriptionRaw(),
                'amount' =>  $amount,
            ],
        ]);
    }

    /**
     * @Route("/split-transaction", name="split_transaction_delete", methods={"DELETE"})
     *
     * @param Request                $request    The request
     * @param EntityManagerInterface $em         The entity manager
     * @param TranslatorInterface    $translator
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $splitTransaction = $em->getRepository(SplitTransaction::class)->findOneBy(['id' => $data['idSplitTransaction'] ?? 0]);
        if (!$splitTransaction) {
            return new JsonResponse([
                'success' => false,
                'errors' => $translator->trans('Could not find split transaction for given id (ID = %transaction_id%)', ['%transaction_id%' => $data['idSplitTransaction'] ?? 0]),
            ]);
        }

        $em->remove($splitTransaction);
        $em->flush();

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
