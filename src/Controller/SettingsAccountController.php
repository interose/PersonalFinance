<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\SubAccount;
use App\Form\AccountEditType;
use App\Form\AccountOnlineType;
use App\Form\AccountType;
use App\Form\SubaccountType;
use App\Lib\FinTsFactory;
use App\Lib\SubAccountHandler;
use App\Lib\TanRequiredException;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/settings")
 */
class SettingsAccountController extends AbstractController
{
    /**
     * @Route("/account", name="settings_account")
     *
     * @param AccountRepository $repository
     *
     * @return Response
     */
    public function indexAction(AccountRepository $repository): Response
    {
        return $this->render('settings_account/index.html.twig', [
            'accounts' => $repository->findAll(),
        ]);
    }

    /**
     * @Route("/account/new", name="settings_account_new")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface  $bag
     * @param AccountRepository      $repository
     *
     * @return Response
     *
     * @throws \Exception If encryption key is not set
     */
    public function newAccountAction(Request $request, EntityManagerInterface $em, ParameterBagInterface $bag, AccountRepository $repository): Response
    {
        $account = new Account();

        $form = $this->createForm(AccountType::class, $account, ['validation_groups' => 'new']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $key = $bag->get('encryptionKey');
            if (empty($key)) {
                throw new \Exception('Encryption key not set!');
            }

            // credentials will be stored encrypted
            $username = $account->getUsername();
            $password = $account->getPassword();
            $account->setUsername('foo');
            $account->setPassword('foo');

            $em->persist($account);
            $em->flush();

            $repository->saveEncrypted($account->getId(), $username, $password, $key);

            return $this->redirectToRoute('settings_account');
        }

        return $this->render('settings_account/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/{id}/edit", name="settings_account_edit")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param Account                $account
     * @param SubAccountHandler      $subAccountHandler
     *
     * @return Response
     */
    public function editAccountAction(Request $request, EntityManagerInterface $em, Account $account, SubAccountHandler $subAccountHandler): Response
    {
        $form = $this->createForm(AccountEditType::class, $account, ['validation_groups' => 'edit']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($account);
            $em->flush();

            return $this->redirectToRoute('settings_account');
        }

        return $this->render('settings_account/edit.html.twig', [
            'form' => $form->createView(),
            'account_id' => $account->getId(),
            'subAccounts' => $subAccountHandler->getSubaccountsAsArray($account),
        ]);
    }

    /**
     * @Route("/account/{id}/edit-credentials", name="settings_account_edit_credentials")
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface  $bag
     * @param Account                $account
     * @param AccountRepository      $repository
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function editOnlineCredentialsAction(Request $request, EntityManagerInterface $em, ParameterBagInterface $bag, Account $account, AccountRepository $repository): Response
    {
        $form = $this->createForm(AccountOnlineType::class, $account, ['validation_groups' => 'edit_credentials']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $key = $bag->get('encryptionKey');
            if (empty($key)) {
                throw new \Exception('Encryption key not set!');
            }

            // credentials will be stored encrypted
            $username = $account->getUsername();
            $password = $account->getPassword();
            $account->setUsername('foo');
            $account->setPassword('foo');

            $em->persist($account);
            $em->flush();

            $repository->saveEncrypted($account->getId(), $username, $password, $key);

            return $this->redirectToRoute('settings_account');
        }

        return $this->render('settings_account/editCredentials.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/{id}/fetch-subaccount", name="settings_account_fetch_subaccount")
     *
     * @param Request             $request
     * @param Account             $account
     * @param FinTsFactory        $finTsFactory
     * @param SubAccountHandler   $subAccountHandler
     * @param TranslatorInterface $translator
     *
     * @return JsonResponse
     */
    public function fetchSubaccountsAction(Request $request, Account $account, FinTsFactory $finTsFactory, SubAccountHandler $subAccountHandler, TranslatorInterface $translator): JsonResponse
    {
        $tan = $request->request->get('tan');
        $bankAccountHandler = $finTsFactory->getAccountHandler($account);

        try {
            $bankAccounts = $bankAccountHandler->getAllAccounts($tan);

            $subAccountHandler->importOrUpdate($bankAccounts, $account);
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

        // render the template part
        $subaccountsView = $this->renderView('settings_account/subAccountList.html.twig', [
            'subAccounts' => $subAccountHandler->getSubaccountsAsArray($account),
        ]);

        return new JsonResponse([
            'success' => true,
            'subAccountsView' => $subaccountsView,
        ]);
    }

    /**
     * @Route("/account/{id}/edit-subaccount", name="settings_account_edit_subaccount")
     *
     * @param Request                $request
     * @param SubAccount             $subAccount
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function editSubaccountAction(Request $request, SubAccount $subAccount, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SubaccountType::class, $subAccount);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($subAccount);
            $em->flush();

            return $this->redirectToRoute('settings_account_edit', [
                'id' => $subAccount->getAccount()->getId(),
            ]);
        }

        return $this->render('settings_account/editSubaccount.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
