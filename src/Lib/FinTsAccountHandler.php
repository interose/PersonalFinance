<?php

namespace App\Lib;

use Fhp\Action\GetBalance;
use Fhp\Model\SEPAAccount;
use Fhp\Model\StatementOfAccount\StatementOfAccount;

class FinTsAccountHandler extends FinTsBase
{
    /**
     * @param \DateTime   $from    The start date for the list of transactions
     * @param \DateTime   $to      The end date for the list of transactions
     * @param SEPAAccount $account The account for which the transactions should be fetched
     * @param string|null $tan     The tan if available
     *
     * @return array Array of transactions
     *
     * @throws TanRequiredException          if action needs a tan
     * @throws \Fhp\CurlException            when the connection fails in a layer below the FinTS protocol
     * @throws \Fhp\Protocol\ServerException when the server responds with a (FinTS-encoded) error message
     * @throws \Exception                    if a previous session could not be created from the session
     */
    public function getByRange(\DateTime $from, \DateTime $to, SEPAAccount $account, ?string $tan = null): array
    {
        $this->init($tan);

        $action = \Fhp\Action\GetStatementOfAccount::create($account, $from, $to);
        $this->execute($action);

        return $this->getTransactions($action->getStatement());
    }

    /**
     * @param string|null $tan
     *
     * @return SEPAAccount[]
     *
     * @throws TanRequiredException
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     */
    public function getAllAccounts(?string $tan = null): array
    {
        $action = $this->init($tan);

        return $this->getAccounts($action);
    }

    /**
     * @param SEPAAccount $account The account for which the balance should be fetched
     * @param string|null $tan     The tan if available
     *
     * @return int The current balance.
     *
     * @throws TanRequiredException          if action needs a tan
     * @throws \Fhp\CurlException            when the connection fails in a layer below the FinTS protocol
     * @throws \Fhp\Protocol\ServerException when the server responds with a (FinTS-encoded) error message
     * @throws \Exception                    if a previous session could not be created from the session
     */
    public function getCurrentBalance(SEPAAccount $account, ?string $tan = null): int
    {
        $this->init($tan);

        $action = GetBalance::create($account, false);
        $this->execute($action);

        $balances = $action->getBalances();
        if (!is_array($balances) || 1 !== count($balances)) {
            throw new \Exception('Could not get balance for first account!');
        }
        /** @var \Fhp\Segment\SAL\HISAL $hisal */
        $hisal = $balances[0];

        return intval($hisal->getGebuchterSaldo()->getAmount() * 100);
    }

    /**
     * @param StatementOfAccount $soa The current StatementOfAccount object
     *
     * @return array
     */
    private function getTransactions(StatementOfAccount $soa): array
    {
        $data = [];

        /** $statement @Statement */
        foreach ($soa->getStatements() as $statement) {
            /* transaction @Transaction */
            $data = array_merge($data, $statement->getTransactions());
        }

        return $data;
    }
}
