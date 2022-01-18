<?php

namespace App\Lib;

use App\Entity\SplitTransaction;
use App\Entity\SubAccount;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;

/**
 * Class GridGenerator.
 */
class GridGenerator
{
    private TransactionRepository $repository;

    /**
     * GridGenerator constructor.
     *
     * @param TransactionRepository $repository The transaction repository
     */
    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param SubAccount $subAccount     The subaccount
     * @param int        $year           The year
     * @param int        $month          The month
     * @param bool       $onlyUnassigned Query for unassigned transactions
     * @param string     $name           Query for transactions LIKE name
     * @param string     $description    Query for transactions LIKE description
     *
     * @return array An array containing the data
     */
    public function getGridData(SubAccount $subAccount, int $year = 0, int $month = 0, bool $onlyUnassigned = false, string $name = '', string $description = ''): array
    {
        $data = [];

        $result = $this->repository->getByYear($subAccount, $year, $month, $onlyUnassigned, $name, $description);
        /** @var Transaction $transaction */
        foreach ($result as $transaction) {
            if ($transaction->getSplitTransactions()->count() > 0) {
                /** @var SplitTransaction $splitTransaction */
                foreach ($transaction->getSplitTransactions() as $splitTransaction) {
                    $category = '';
                    if ($splitTransaction->getCategory()) {
                        $category = $splitTransaction->getCategory()->getName();
                        if ($splitTransaction->getCategory()->getCategoryGroup()) {
                            $category = sprintf('%s:%s', $splitTransaction->getCategory()->getCategoryGroup()->getName(), $category);
                        }
                    }

                    $data[] = [
                        'id_transaction' => $transaction->getId(),
                        'id_splittransaction' => $splitTransaction->getId(),
                        'amount' => $splitTransaction->getAmount(),
                        'name' => $transaction->getName(),
                        'description' => $splitTransaction->getDescription(),
                        'description_raw' => $splitTransaction->getDescription(),
                        'booking_text' => $transaction->getBookingText(),
                        'credit_debit' => $transaction->getCreditDebit(),
                        'valuta_date' => $splitTransaction->getValutaDate()->format('Y-m-d'),
                        'category_id' => $splitTransaction->getCategory() ? $splitTransaction->getCategory()->getId() : '',
                        'category_name' => $category,
                        'split' => true,
                    ];
                }
            } else {
                $category = '';
                if ($transaction->getCategory()) {
                    $category = $transaction->getCategory()->getName();
                    if ($transaction->getCategory()->getCategoryGroup()) {
                        $category = sprintf('%s:%s', $transaction->getCategory()->getCategoryGroup()->getName(), $category);
                    }
                }

                $name = $transaction->getName();
                $payPalTransaction = $transaction->getPayPalTransaction();
                if (!is_null($payPalTransaction)) {
                    $name .= sprintf(' (%s)', $payPalTransaction->getName());
                }

                $data[] = [
                    'id_transaction' => $transaction->getId(),
                    'id_splittransaction' => 0,
                    'amount' => $transaction->getAmount(),
                    'name' => $name,
                    'description' => $transaction->getDescription(),
                    'description_raw' => $transaction->getDescriptionRaw(),
                    'booking_text' => $transaction->getBookingText(),
                    'credit_debit' => $transaction->getCreditDebit(),
                    'valuta_date' => $transaction->getValutaDate()->format('Y-m-d'),
                    'category_id' => $transaction->getCategory() ? $transaction->getCategory()->getId() : '',
                    'category_name' => $category,
                    'split' => false,
                ];
            }
        }

        return $data;
    }
}
