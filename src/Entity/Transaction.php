<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    const DO_NOT_SHOW = 30;
    public const GROUPING_YEARLY = 1;
    public const GROUPING_MONTHLY = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bookingDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $valutaDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $creditDebit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bookingText;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descriptionRaw;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bankCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accountNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="transaction")
     */
    private $category;

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private $checksum;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SplitTransaction", mappedBy="transaction", orphanRemoval=true)
     */
    private $splitTransactions;

    /**
     * @ORM\ManyToOne(targetEntity=SubAccount::class, inversedBy="transactions")
     */
    private $subAccount;

    public function __construct()
    {
        $this->splitTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->bookingDate;
    }

    public function setBookingDate(\DateTimeInterface $bookingDate): self
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }

    public function getValutaDate(): ?\DateTimeInterface
    {
        return $this->valutaDate;
    }

    public function setValutaDate(\DateTimeInterface $valutaDate): self
    {
        $this->valutaDate = $valutaDate;

        return $this;
    }

    public function getCreditDebit(): ?string
    {
        return $this->creditDebit;
    }

    public function setCreditDebit(?string $creditDebit): self
    {
        $this->creditDebit = $creditDebit;

        return $this;
    }

    public function getBookingText(): ?string
    {
        return $this->bookingText;
    }

    public function setBookingText(?string $bookingText): self
    {
        $this->bookingText = $bookingText;

        return $this;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(?string $bankCode): self
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getChecksum(): ?string
    {
        return $this->checksum;
    }

    public function setChecksum(string $checksum): self
    {
        $this->checksum = $checksum;

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

    public function getDescriptionRaw(): ?string
    {
        return $this->descriptionRaw;
    }

    public function setDescriptionRaw(?string $descriptionRaw): self
    {
        $this->descriptionRaw = $descriptionRaw;

        return $this;
    }

    /**
     * @return Collection|SplitTransaction[]
     */
    public function getSplitTransactions(): Collection
    {
        return $this->splitTransactions;
    }

    public function addSplitTransaction(SplitTransaction $splitTransaction): self
    {
        if (!$this->splitTransactions->contains($splitTransaction)) {
            $this->splitTransactions[] = $splitTransaction;
            $splitTransaction->setTransaction($this);
        }

        return $this;
    }

    public function removeSplitTransaction(SplitTransaction $splitTransaction): self
    {
        if ($this->splitTransactions->contains($splitTransaction)) {
            $this->splitTransactions->removeElement($splitTransaction);
            // set the owning side to null (unless already changed)
            if ($splitTransaction->getTransaction() === $this) {
                $splitTransaction->setTransaction(null);
            }
        }

        return $this;
    }

    public function getSubAccount(): ?SubAccount
    {
        return $this->subAccount;
    }

    public function setSubAccount(?SubAccount $subAccount): self
    {
        $this->subAccount = $subAccount;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount/100;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
