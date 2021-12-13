<?php

namespace App\Entity;

use App\Repository\SubAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Fhp\Model\SEPAAccount;

/**
 * @ORM\Entity(repositoryClass=SubAccountRepository::class)
 */
class SubAccount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $iban;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bic;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $accountNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $blz;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="subAccounts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="subAccount")
     */
    private $transactions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isEnabled;

    /**
     * @ORM\OneToOne(targetEntity=CurrentBalance::class, mappedBy="subAccount")
     */
    private $currentBalance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * Transforms the database record to a SEPAAccount object
     *
     * @return SEPAAccount
     */
    public function getSEPAAcount(): SEPAAccount
    {
        $sepaAccount = new SEPAAccount();

        $sepaAccount
            ->setIban($this->getIban())
            ->setBic($this->getBic())
            ->setBlz($this->getBlz())
            ->setAccountNumber($this->getAccountNumber())
        ;

        return $sepaAccount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(string $bic): self
    {
        $this->bic = $bic;

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getBlz(): ?string
    {
        return $this->blz;
    }

    public function setBlz(string $blz): self
    {
        $this->blz = $blz;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setSubAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getSubAccount() === $this) {
                $transaction->setSubAccount(null);
            }
        }

        return $this;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getCurrentBalance(): ?CurrentBalance
    {
        return $this->currentBalance;
    }

    public function setCurrentBalance(?CurrentBalance $currentBalance): self
    {
        $this->currentBalance = $currentBalance;

        // set (or unset) the owning side of the relation if necessary
        $newSubAccount = null === $currentBalance ? null : $this;
        if ($currentBalance->getSubAccount() !== $newSubAccount) {
            $currentBalance->setSubAccount($newSubAccount);
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
