<?php

namespace App\Lib;

use Fhp\Action\SendSEPATransfer;
use Fhp\Model\SEPAAccount;
use nemiah\phpSepaXml\SEPACreditor;
use nemiah\phpSepaXml\SEPADebitor;
use nemiah\phpSepaXml\SEPATransfer;

class FinTsTransferHandler extends FinTsBase
{
    private string $accountHolder;
    private string $accountHolderIban;
    private string $accountHolderBic;

    /**
     * @param SEPAAccount $account
     * @param array|null  $params
     * @param string|null $tan
     *
     * @throws TanRequiredException
     * @throws \Fhp\CurlException
     * @throws \Fhp\Protocol\ServerException
     * @throws \Exception
     */
    public function transfer(SEPAAccount $account, ?array $params = [], ?string $tan = null)
    {
        $action = $this->init($tan);

        $this->validateAccountHolder();

        if (!$action instanceof SendSEPATransfer) {
            $sepaDD = $this->prepareSepaTransfer($params);
            $action = SendSEPATransfer::create($account, $sepaDD->toXML());

            $this->execute($action);
        }

        $action->ensureDone();
    }

    /**
     * @param string $accountHolder
     */
    public function setAccountHolder(string $accountHolder): void
    {
        $this->accountHolder = $accountHolder;
    }

    /**
     * @param string $accountHolderIban
     */
    public function setAccountHolderIban(string $accountHolderIban): void
    {
        $this->accountHolderIban = $accountHolderIban;
    }

    /**
     * @param string $accountHolderBic
     */
    public function setAccountHolderBic(string $accountHolderBic): void
    {
        $this->accountHolderBic = $accountHolderBic;
    }

    /**
     * @throws \Exception if account holder information is not set
     */
    private function validateAccountHolder()
    {
        if (empty($this->accountHolder) || empty($this->accountHolderIban) || empty($this->accountHolderBic)) {
            throw new \Exception('Accountholder information has to be set!');
        }
    }

    /**
     * @param array $params
     *
     * @return SEPATransfer
     *
     * @throws \Exception
     */
    private function prepareSepaTransfer(array $params = []): SEPATransfer
    {
        if (!isset($params['info']) || empty($params['info'])) {
            throw new \Exception('Info for sepa transfer is missing');
        }
        if (!isset($params['name']) || empty($params['name'])) {
            throw new \Exception('Name for sepa transfer is missing');
        }
        if (!isset($params['iban']) || empty($params['iban'])) {
            throw new \Exception('IBAN for sepa transfer is missing');
        }
        if (!isset($params['bic']) || empty($params['bic'])) {
            throw new \Exception('BIC for sepa transfer is missing');
        }
        if (!isset($params['amount']) || empty($params['amount'])) {
            throw new \Exception('Amount for sepa transfer is missing');
        }

        $dt = new \DateTime();
        $dt->add(new \DateInterval('P1D'));
        $sepaTransfer = new SEPATransfer([
            'messageID' => time(),
            'paymentID' => time(),
        ]);

        $sepaTransfer->setDebitor(new SEPADebitor([ //this is you
            'name' => $this->accountHolder,
            'iban' => $this->accountHolderIban,
            'bic' => $this->accountHolderBic,
        ]));

        $sepaTransfer->addCreditor(new SEPACreditor([ //this is who you want to send money to
            'info' => $params['info'],
            'name' => $params['name'],
            'iban' => $params['iban'],
            'bic' => $params['bic'],
            'amount' => $params['amount'],
            'currency' => 'EUR',
            'reqestedExecutionDate' => $dt,
        ]));

        return $sepaTransfer;
    }
}
