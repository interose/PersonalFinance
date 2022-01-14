<?php

namespace App\Controller;

use App\Lib\Importer\PayPalImporter;
use App\Repository\PayPalTransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}