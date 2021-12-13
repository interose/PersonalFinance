<?php

namespace App\Lib;

use App\Entity\Category;
use App\Entity\SplitTransaction;
use App\Entity\SubAccount;
use Doctrine\ORM\EntityManagerInterface;
use Fhp\Model\StatementOfAccount\Transaction;

/**
 * Class TransactionHandler.
 */
class TransactionHandler
{
    private EntityManagerInterface $em;
    private CategoryEvaluator $evaluator;
    private int $iNew;
    private int $iAssigned;
    private int $iTransactions;

    /**
     * TransactionHandler constructor.
     *
     * @param EntityManagerInterface $em        The entity manager
     * @param CategoryEvaluator      $evaluator The category evaluater object
     */
    public function __construct(EntityManagerInterface $em, CategoryEvaluator $evaluator)
    {
        $this->em = $em;
        $this->evaluator = $evaluator;
    }

    /**
     * @param array      $data       The data array from the account handler
     * @param SubAccount $subAccount
     */
    public function import(array $data, SubAccount $subAccount)
    {
        $this->iNew = $this->iAssigned = 0;
        $this->iTransactions = count($data);

        /** @var Transaction $transaction */
        foreach ($data as $transaction) {
            if ($transaction->getBooked() !== true) {
                // skip not booked transactions
                continue;
            }

            if (false === $this->isDuplicate($transaction, $subAccount)) {
                ++$this->iNew;
                $category = $this->evaluator->evaluate($transaction);
                $this->saveTransaction($transaction, $subAccount, $category);
            }
        }
    }

    /**
     * @param int|null $categoryId         The category id
     * @param int|null $transactionId      The transaction which should be updated
     * @param int|null $splitTransactionId Or the splittransaction which should be updated
     *
     * @throws \Exception
     */
    public function update(int $categoryId = null, int $transactionId = null, int $splitTransactionId = null)
    {
        $repo = $this->em->getRepository(Category::class);
        $category = $repo->findOneBy(['id' => $categoryId]);

        if (!$category) {
            throw new \Exception(sprintf('There is no category for the given key: %d', $categoryId));
        }

        $obj = null;
        if (!is_null($splitTransactionId)) {
            /** @var SplitTransaction $obj */
            $obj = $this->em->getRepository(SplitTransaction::class)->findOneBy(['id' => $splitTransactionId]);
        } elseif (!is_null($transactionId)) {
            /** @var Transaction $obj */
            $obj = $this->em->getRepository(\App\Entity\Transaction::class)->findOneBy(['id' => $transactionId]);
        }

        if ($obj) {
            $obj->setCategory($category);

            $this->em->persist($obj);
            $this->em->flush();
        }
    }

    /**
     * @return int The amount of new transactions
     */
    public function getINew(): int
    {
        return $this->iNew;
    }

    /**
     * @return int The amount of assigned transactions
     */
    public function getIAssigned(): int
    {
        return $this->iAssigned;
    }

    /**
     * @return int The amount of transactions
     */
    public function getITransactions(): int
    {
        return $this->iTransactions;
    }

    /**
     * @param Transaction $transaction A single transaction
     * @param SubAccount  $subAccount
     *
     * @return bool True if transaction exists, otherwise false
     */
    private function isDuplicate(Transaction $transaction, SubAccount $subAccount): bool
    {
        $checksum = $this->generateChecksum($transaction, $subAccount);
        $entityTransaction = $this->em->getRepository(\App\Entity\Transaction::class)->findOneBy(['checksum' => $checksum]);

        return !is_null($entityTransaction);
    }

    /**
     * @param Transaction $transaction A single transaction
     * @param SubAccount  $subAccount
     *
     * @return string A checksum
     */
    private function generateChecksum(Transaction $transaction, SubAccount $subAccount): string
    {
        return md5(
            $transaction->getValutaDate()->format('d.m.Y').$transaction->getAmount().$transaction->getCreditDebit().$transaction->getBookingText().$transaction->getDescription1().$transaction->getDescription2().$transaction->getBankCode().$transaction->getAccountNumber().$transaction->getName().$subAccount->getAccountNumber()
        );
    }

    /**
     * @param Transaction $transaction A single transaction
     * @param SubAccount  $subAccount
     * @param int|null    $category    The category id
     */
    private function saveTransaction(Transaction $transaction, SubAccount $subAccount, int $category = null)
    {
        $desc = explode('+', $transaction->getDescription1());

        $obj = new \App\Entity\Transaction();
        $obj
            ->setBookingDate($transaction->getBookingDate())
            ->setValutaDate($transaction->getValutaDate())
            ->setAmount(intval($transaction->getAmount() * 100))
            ->setCreditDebit($transaction->getCreditDebit())
            ->setBookingText($transaction->getBookingText())
            ->setDescription($desc[count($desc) - 1])
            ->setDescriptionRaw($transaction->getDescription1())
            ->setBankCode($transaction->getBankCode())
            ->setAccountNumber($transaction->getAccountNumber())
            ->setName($transaction->getName())
            ->setSubAccount($subAccount)
            ->setChecksum($this->generateChecksum($transaction, $subAccount))
        ;

        if ($category) {
            $categoryObj = $this->em->getRepository(Category::class)->findOneBy(['id' => $category]);
            if ($categoryObj) {
                ++$this->iAssigned;
                $obj->setCategory($categoryObj);
            }
        }

        $this->em->persist($obj);
        $this->em->flush();
    }
}
