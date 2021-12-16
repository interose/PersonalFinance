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
        if (strlen($this->accountHolder) === 0) {
            throw new \Exception('Accountholder has to be set!');
        }

        if (strlen($this->accountHolderIban) === 0) {
            throw new \Exception('Accountholder IBAN has to be set!');
        }

        if (strlen($this->accountHolderBic) === 0) {
            throw new \Exception('Accountholder BIC has to be set!');
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
        $info = $params['info'] ?? '';
        if (strlen($info) === 0) {
            throw new \Exception('Info for sepa transfer is missing');
        }

        $name = $params['name'] ?? '';
        if (strlen($name) === 0) {
            throw new \Exception('Name for sepa transfer is missing');
        }

        $iban = $params['iban'] ?? '';
        if (strlen($iban) === 0) {
            throw new \Exception('IBAN for sepa transfer is missing');
        }

        $bic = $params['bic'] ?? '';
        if (strlen($bic) === 0) {
            throw new \Exception('BIC for sepa transfer is missing');
        }

        $amount = $params['amount'] ?? 0;
        if (0 === $amount) {
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
