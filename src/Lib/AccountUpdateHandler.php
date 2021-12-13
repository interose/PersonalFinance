<?php

namespace App\Lib;

use App\Repository\CurrentBalanceRepository;
use App\Repository\SubAccountRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class AccountUpdateHandler
{
    private RequestStack $requestStack;
    private SubAccountRepository $subAccountRepository;
    private CurrentBalanceRepository $balanceRepository;
    private FinTsFactory $finTsFactory;
    private TransactionHandler $transactionHandler;

    const UPDATE_PROGRESS_SESSION_KEY = 'fints_progress';
    const RESULT_SESSION_KEY = 'fints_import_result';
    const UPDATE_BALANCE_POSTFIX = '_balance';
    const UPDATE_TRANSACTION_POSTFIX = '_transaction';
    const UPDATE_DEFAULT_RANGE_IN_DAYS = '10';

    /**
     * AccountUpdateHandler constructor.
     *
     * @param RequestStack             $requestStack
     * @param SubAccountRepository     $subAccountRepository
     * @param CurrentBalanceRepository $balanceRepository
     * @param FinTsFactory             $finTsFactory
     * @param TransactionHandler       $transactionHandler
     */
    public function __construct(RequestStack $requestStack, SubAccountRepository $subAccountRepository, CurrentBalanceRepository $balanceRepository, FinTsFactory $finTsFactory, TransactionHandler $transactionHandler)
    {
        $this->requestStack = $requestStack;
        $this->subAccountRepository = $subAccountRepository;
        $this->balanceRepository = $balanceRepository;
        $this->finTsFactory = $finTsFactory;
        $this->transactionHandler = $transactionHandler;
    }

    /**
     * @param string|null $tan
     *
     * @throws TanRequiredException
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     *
     * @return array
     */
    public function updateAll(?string $tan = null): array
    {
        $subAccounts = $this->subAccountRepository->findBy(['isEnabled' => true], ['id' => 'ASC']);

        // so tan required exception could happen somewhere within the update process,
        // we have to keep track of the process itself
        $session = $this->requestStack->getSession();
        $progress = $session->get(self::UPDATE_PROGRESS_SESSION_KEY, []);

        $result = $session->get(self::RESULT_SESSION_KEY, []);

        $to = new \DateTime();
        $from = new \DateTime();
        $from->modify(sprintf('- %d days', self::UPDATE_DEFAULT_RANGE_IN_DAYS));

        try {
            foreach ($subAccounts as $subAccount) {
                $iban = $subAccount->getIban();
                $sepaAccount = $subAccount->getSEPAAcount();
                $baseAccount = $subAccount->getAccount();
                $accountHandler = $this->finTsFactory->getAccountHandler($baseAccount);

                if (!in_array($iban.self::UPDATE_BALANCE_POSTFIX, $progress)) {
                    $balance = $accountHandler->getCurrentBalance($sepaAccount, $tan);
                    $this->balanceRepository->updateBalance($subAccount, $balance);

                    // via the session progress variable, we automatically jump to the last action which has
                    // raised the TanRequiredException. so after using the tan, we have to clear it so it will not
                    // be used for the next action
                    $tan = null;
                    $progress[] = $iban.self::UPDATE_BALANCE_POSTFIX;
                }

                if (!in_array($iban.self::UPDATE_TRANSACTION_POSTFIX, $progress)) {
                    $data = $accountHandler->getByRange($from, $to, $sepaAccount, $tan);
                    $this->transactionHandler->import($data, $subAccount);

                    // via the session progress variable, we automatically jump to the last action which has
                    // raised the TanRequiredException. so after using the tan, we have to clear it so it will not
                    // be used for the next action
                    $tan = null;
                    $progress[] = $iban.self::UPDATE_TRANSACTION_POSTFIX;

                    $result[] = [
                        'accountName' => $subAccount->getAccount()->getName(),
                        'accountNumber' => $subAccount->getAccountNumber(),
                        'transactions' => $this->transactionHandler->getITransactions(),
                        'assigned' => $this->transactionHandler->getIAssigned(),
                        'new' => $this->transactionHandler->getINew(),
                    ];
                }
            }
        } catch (TanRequiredException $e) {
            // we have to keep track of the current progress. so if a tan is required from the user
            // save the current progress within the session and bubble the same exception one level
            // up in order to show the user a tan input form
            $session->set(self::UPDATE_PROGRESS_SESSION_KEY, $progress);

            $session->set(self::RESULT_SESSION_KEY, $result);

            throw $e;
        }

        $session->remove(self::UPDATE_PROGRESS_SESSION_KEY);

        return $result;
    }
}
