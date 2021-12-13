<?php

namespace App\Lib;

use App\Entity\SplitTransaction;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SplitTransactionHandler.
 */
class SplitTransactionHandler
{
    private EntityManagerInterface $em;
    private Transaction $transaction;

    /**
     * SplitTransactionHandler constructor.
     *
     * @param EntityManagerInterface $em The entity manager
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array An array containing the data of the transaction and the child transactions if any
     */
    public function getChildTransactions(): array
    {
        $data = [];

        if ($this->transaction->getSplitTransactions()->count() > 0) {
            /** @var SplitTransaction $splitTransaction */
            foreach ($this->transaction->getSplitTransactions() as $splitTransaction) {
                $data[] = [
                    'transaction' => $splitTransaction->getTransaction()->getId(),
                    'idSplitTransaction' => $splitTransaction->getId(),
                    'description' => $splitTransaction->getDescription(),
                    'amount' => $splitTransaction->getAmount(),
                    'category_name' => $splitTransaction->hasCategory() ? $splitTransaction->getCategory()->getName() : '',
                    'category_id' => $splitTransaction->hasCategory() ? $splitTransaction->getCategory()->getId() : 0,
                    'valuta_date' => $splitTransaction->getValutaDate()->format('Y-m-d'),
                ];
            }
        }

        return $data;
    }

    /**
     * @param int|null $id The id of the transaction object
     *
     * @throws \Exception If transaction for given key could not be found
     */
    public function setTransactionId(int $id = null)
    {
        $this->transaction = $this->em->getRepository(Transaction::class)->findOneBy(['id' => $id]);

        if (!$this->transaction) {
            throw new \Exception(sprintf('No transaction for given key: %d', $id));
        }
    }

    /**
     * @return Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }
}
