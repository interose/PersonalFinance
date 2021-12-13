<?php

namespace App\Lib;

use App\Entity\Account;
use App\Entity\SubAccount;
use App\Repository\SubAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Fhp\Model\SEPAAccount;
use JetBrains\PhpStorm\Pure;

class SubAccountHandler
{
    private EntityManagerInterface $em;
    private SubAccountRepository $subAccountRepository;

    /**
     * SubAccountHandler constructor.
     *
     * @param EntityManagerInterface $em
     * @param SubAccountRepository   $repository
     */
    public function __construct(EntityManagerInterface $em, SubAccountRepository $repository)
    {
        $this->em = $em;
        $this->subAccountRepository = $repository;
    }

    /**
     * @param SEPAAccount[] $sepaAccounts
     * @param Account       $account
     */
    public function importOrUpdate(array $sepaAccounts, Account $account)
    {
        foreach ($sepaAccounts as $bankAccount) {
            /** @var SubAccount|null $subAccount */
            $subAccount = $this->subAccountRepository->findOneBy(['iban' => $bankAccount->getIban()]);

            if (is_null($subAccount)) {
                $subAccount = new SubAccount();
            }

            $subAccount->setIban($bankAccount->getIban());
            $subAccount->setBic($bankAccount->getBic());
            $subAccount->setAccount($account);
            $subAccount->setAccountNumber($bankAccount->getAccountNumber());
            $subAccount->setBlz($bankAccount->getBlz());
            $subAccount->setIsEnabled(true);

            $this->em->persist($subAccount);
            $this->em->flush();

            unset($subAccount);
        }
    }

    /**
     * @param Account $account
     *
     * @return array
     */
    public function getSubaccountsAsArray(Account $account): array
    {
        $data = [];

        foreach ($account->getSubAccounts() as $subAccount) {
            $data[] = [
                'iban' => $subAccount->getIban(),
                'blz' => $subAccount->getBlz(),
                'accountNumber' => $subAccount->getAccountNumber(),
                'bic' => $subAccount->getBic(),
                'isEnabled' => $subAccount->getIsEnabled(),
                'description' => $subAccount->getDescription(),
                'id' => $subAccount->getId(),
            ];
        }

        return $data;
    }
}
