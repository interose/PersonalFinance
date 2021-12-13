<?php

namespace App\Controller;

use App\Entity\SubAccount;
use App\Entity\Transfer;
use App\Form\TransferTanType;
use App\Form\TransferType;
use App\Lib\FinTsFactory;
use App\Lib\SettingsHandler;
use App\Lib\TanRequiredException;
use App\Repository\TransferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/transfer")
 */
class TransferController extends AbstractController
{
    /**
     * @Route("/", name="transfer_index")
     *
     * @param Request                $request         The request
     * @param EntityManagerInterface $em              The entity manager
     * @param FinTsFactory           $finTsFactory    The factory class for creating the finTs objects
     * @param SettingsHandler        $settingsHandler
     *
     * @return Response The rendered template
     */
    public function transferAction(Request $request, EntityManagerInterface $em, FinTsFactory $finTsFactory, SettingsHandler $settingsHandler): Response
    {
        $transfer = new Transfer();
        $subAccount = $em->getRepository(SubAccount::class)->findOneBy(['id' => $settingsHandler->getMainAccount()]);
        $sepaAccount = $subAccount->getSEPAAcount();

        if ($request->query->has('id')) {
            $transferDraft = $em->getRepository(Transfer::class)->findOneBy(['id' => $request->query->getInt('id')]);
            if ($transferDraft) {
                $transfer->setName($transferDraft->getName());
                $transfer->setBankName($transferDraft->getBankName());
                $transfer->setIban($transferDraft->getIban());
                $transfer->setBic($transferDraft->getBic());
            }
        }

        $form = $this->createForm(TransferType::class, $transfer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $today = new \DateTime();
            $transfer->setExecutionDate($today);
            $em->persist($transfer);
            $em->flush();

            $transferHandler = $finTsFactory->getTransferHandler($subAccount);

            try {
                $transferHandler->transfer($sepaAccount, [
                    'info' => $transfer->getInfo(),
                    'name' => $transfer->getName(),
                    'iban' => $transfer->getIban(),
                    'bic' => $transfer->getBic(),
                    'amount' => $transfer->getAmount(),
                ], null);
            } catch (TanRequiredException $e) {
                return $this->redirectToRoute('transfer_tan');
            } catch (\Exception $e) {
                return $this->render('transfer/error.html.twig', [
                    'msg' => $e->getMessage(),
                ]);
            }
        }

        return $this->render('transfer/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/tan", name="transfer_tan")
     *
     * @param Request                $request         The request object
     * @param FinTsFactory           $finTsFactory    The factory class for creating the finTs objects
     * @param EntityManagerInterface $em              The factory class for creating the finTs objects
     * @param TranslatorInterface    $translator
     * @param SettingsHandler        $settingsHandler
     *
     * @return Response The rendered template
     */
    public function tanAction(Request $request, FinTsFactory $finTsFactory, EntityManagerInterface $em, TranslatorInterface $translator, SettingsHandler $settingsHandler): Response
    {
        $subAccount = $em->getRepository(SubAccount::class)->findOneBy(['id' => $settingsHandler->getMainAccount()]);
        $sepaAccount = $subAccount->getSEPAAcount();

        $form = $this->createForm(TransferTanType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!isset($data['tan'])) {
                return $this->render('transfer/tanError.html.twig', [
                    'error_message' => $translator->trans('Please provide a valid TAN!'),
                ]);
            }

            try {
                $transferHandler = $finTsFactory->getTransferHandler($subAccount);

                $transferHandler->transfer($sepaAccount, null, $data['tan']);
            } catch (TanRequiredException $e) {
                return $this->redirectToRoute('transfer_tan');
            } catch (\Exception $e) {
                return $this->render('transfer/error.html.twig', [
                    'msg' => $e->getMessage(),
                ]);
            }

            return $this->redirectToRoute('transfer_success');
        }

        return $this->render('transfer/tan.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/success", name="transfer_success")
     *
     * @return Response
     */
    public function transferSuccessAction(): Response
    {
        return $this->render('transfer/success.html.twig');
    }

    /**
     * @Route("/search", name="transfer_autocomplete")
     *
     * @param Request            $request    The request
     * @param TransferRepository $repository The repository
     *
     * @return JsonResponse
     */
    public function autoCompleteAction(Request $request, TransferRepository $repository): JsonResponse
    {
        $query = $request->query->get('q');
        $items = [];
        if (!is_null($query)) {
            $items = $repository->findByName($query);
        }

        return new JsonResponse($items);
    }

    /**
     * @Route("/list", name="transfer_list")
     *
     * @param TransferRepository $repository
     *
     * @return Response
     */
    public function transferListAction(TransferRepository $repository): Response
    {
        return $this->render('transfer/list.html.twig');
    }

    /**
     * @Route("/data", name="transfer_data")
     *
     * @param TransferRepository $repository
     *
     * @return JsonResponse
     */
    public function transferListDataAction(TransferRepository $repository): JsonResponse
    {
        $data = array_map(function (Transfer $item) {
            return [
                'id' => $item->getId(),
                'info' => $item->getInfo(),
                'name' => $item->getName(),
                'iban' => $item->getIban(),
                'bic' => $item->getBic(),
                'amount' => $item->getAmount(),
                'executionDate' => $item->getExecutionDate()->format('Y-m-d'),
                'bankName' => $item->getBankName(),
            ];
        }, $repository->findBy([], ['executionDate' => 'DESC']));

        return new JsonResponse(['success' => true, 'data' => $data]);
    }
}
