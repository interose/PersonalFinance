<?php

namespace App\Lib;

use App\Entity\SplitTransaction;
use App\Entity\Transaction;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SplitTransactionHandler.
 */
class SplitTransactionHelper
{
    /**
     * @param Transaction $transaction
     * @return array
     */
    public function serialize(Transaction $transaction): array
    {
        $data = [];

        foreach ($transaction->getSplitTransactions() as $splitTransaction) {
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

        return $data;
    }

    /**
     * @param Transaction $transaction
     * @return float
     */
    public function calcRemaining(Transaction $transaction): float
    {
        $sum = 0;

        foreach ($transaction->getSplitTransactions() as $splitTransaction) {
            $sum += $splitTransaction->getAmount();
        }

        return $transaction->getAmount() - $sum;
    }
}
