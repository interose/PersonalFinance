<?php

namespace App\Controller;

use App\Lib\Importer\PayPalImporter;
use App\Repository\PayPalTransactionRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/paypal")
 */
class PayPalController extends AbstractController
{
    /**
     * @Route("/", name="paypal_index")
     *
     * @return Response The rendered template
     */
    public function indexAction(): Response
    {
        return $this->render('paypal/index.html.twig');
    }

    /**
     * @Route("/data", name="paypal_data", methods={"GET"})
     *
     * @param Request $request
     * @param PayPalTransactionRepository $repository
     *
     * @return JsonResponse
     */
    public function getDataAction(Request $request, PayPalTransactionRepository $repository): JsonResponse
    {
        try {
            $year = $request->query->getInt('year', intval(date('Y')));
            $data = $repository->getByYear($year);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return new JsonResponse(['success' => true, 'data' => $data]);
    }

    /**
     * @Route("/import", name="paypal_import", methods={"POST"})
     *
     * @param Request $request
     * @param PayPalImporter $importer
     *
     * @return JsonResponse
     */
    public function importCsvAction(Request $request, PayPalImporter $importer): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        try {
            $successMsg = $importer->import($uploadedFile);
        } catch (\Exception $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }

        return new JsonResponse(['success' => true, 'message' => $successMsg]);
    }

    /**
     * @Route("/get-transaction-data", name="paypal_get_transaction_data")
     *
     * @param Request $request
     * @param PayPalTransactionRepository $repository
     * @param TransactionRepository $transactionRepository
     *
     * @return JsonResponse
     */
    public function getTransactionDataAction(Request $request, PayPalTransactionRepository $repository, TransactionRepository $transactionRepository): JsonResponse
    {
        $payPalTransaction = $repository->findOneBy(['id' => $request->query->getInt('idPayPalTransaction')]);
        if (is_null($payPalTransaction)) {
            return new JsonResponse(['success' => false, 'message' => 'error message']);
        }

        return new JsonResponse(['success' => true, 'data' => $transactionRepository->getPayPalTransactions($payPalTransaction->getBookingDate())]);
    }

    /**
     * @Route("/assign-transaction", name="paypal_assign_transaction", methods={"POST"})
     *
     * @param Request $request
     * @param PayPalTransactionRepository $payPalTransactionRepository
     * @param TransactionRepository $transactionRepository
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    public function assignTransactionAction(Request $request, PayPalTransactionRepository $payPalTransactionRepository, TransactionRepository $transactionRepository, EntityManagerInterface $em, TranslatorInterface $translator): JsonResponse
    {
        $transaction = $transactionRepository->findOneBy(['id' => $request->request->getInt('idTransaction')]);
        if (is_null($transaction)) {
            return new JsonResponse([
                'success' => false,
                'message' => $translator->trans('Could not find transaction for given id (ID = %transaction_id%)', ['%transaction_id' => $request->request->getInt('idTransaction')]),
            ]);
        }

        $payPalTransaction = $payPalTransactionRepository->findOneBy(['id' => $request->request->getInt('idPayPalTransaction')]);
        if (is_null($payPalTransaction)) {
            return new JsonResponse([
                'success' => false,
                'message' => $translator->trans('Could not find PayPal transaction for given id (ID = %transaction_id%)', ['%transaction_id' => $request->request->getInt('idPayPalTransaction')]),
            ]);
        }

        try {
            $transaction->setPayPalTransaction($payPalTransaction);
            $em->persist($transaction);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }

        return new JsonResponse(['success' => true]);
    }
}