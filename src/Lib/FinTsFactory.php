<?php

namespace App\Lib;

use App\Entity\Account;
use App\Entity\SubAccount;
use App\Repository\AccountRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FinTsFactory
{
    private AccountRepository $accountRepository;
    private RequestStack $requestStack;
    private ParameterBagInterface $parameterBag;

    /**
     * FinTsFactory constructor.
     *
     * @param AccountRepository     $repository
     * @param RequestStack          $requestStack
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(AccountRepository $repository, RequestStack $requestStack, ParameterBagInterface $parameterBag)
    {
        $this->accountRepository = $repository;
        $this->requestStack = $requestStack;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param Account $account
     *
     * @return FinTsAccountHandler
     */
    public function getAccountHandler(Account $account): FinTsAccountHandler
    {
        list($username, $pin) = $this->getAccountCredentials($account);

        return new FinTsAccountHandler(
            $this->requestStack,
            $account->getUrl(),
            $account->getBankCode(),
            $username,
            $pin,
            $this->parameterBag->get('product_name'),
            $this->parameterBag->get('product_version'),
            $account->getTanMechanism(),
            $account->getTanMediaName()
        );
    }

    /**
     * @param SubAccount $subAccount
     *
     * @return FinTsTransferHandler
     */
    public function getTransferHandler(SubAccount $subAccount): FinTsTransferHandler
    {
        $account = $subAccount->getAccount();
        list($username, $pin) = $this->getAccountCredentials($account);

        $transferHandler = new FinTsTransferHandler(
            $this->requestStack,
            $account->getUrl(),
            $account->getBankCode(),
            $username,
            $pin,
            $this->parameterBag->get('product_name'),
            $this->parameterBag->get('product_version'),
            $account->getTanMechanism(),
            $account->getTanMediaName()
        );

        $transferHandler->setAccountHolder($account->getAccountHolder());
        $transferHandler->setAccountHolderBic($account->getBic());
        $transferHandler->setAccountHolderIban($account->getIban());

        return $transferHandler;
    }

    /**
     * @param Account $account
     *
     * @return array
     */
    private function getAccountCredentials(Account $account): array
    {
        return $this->accountRepository->getEncrypted($account->getId(), $this->parameterBag->get('encryptionKey'));
    }
}
