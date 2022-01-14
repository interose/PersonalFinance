<?php

namespace App\Entity;

use App\Repository\PayPalTransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PayPalTransactionRepository::class)
 */
class PayPalTransaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $booking_date;

    /**
     * @ORM\Column(type="time")
     */
    private $booking_time;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recipient;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $transactionCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $associatedTransactionCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $invoiceNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->booking_date;
    }

    public function setBookingDate(\DateTimeInterface $booking_date): self
    {
        $this->booking_date = $booking_date;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getTransactionCode(): ?string
    {
        return $this->transactionCode;
    }

    public function setTransactionCode(string $transactionCode): self
    {
        $this->transactionCode = $transactionCode;

        return $this;
    }

    public function getArticleDescription(): ?string
    {
        return $this->articleDescription;
    }

    public function setArticleDescription(string $articleDescription): self
    {
        $this->articleDescription = $articleDescription;

        return $this;
    }

    public function getArticleNumber(): ?string
    {
        return $this->articleNumber;
    }

    public function setArticleNumber(string $articleNumber): self
    {
        $this->articleNumber = $articleNumber;

        return $this;
    }

    public function getAssociatedTransactionCode(): ?string
    {
        return $this->associatedTransactionCode;
    }

    public function setAssociatedTransactionCode(string $associatedTransactionCode): self
    {
        $this->associatedTransactionCode = $associatedTransactionCode;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBookingTime(): ?\DateTimeInterface
    {
        return $this->booking_time;
    }

    public function setBookingTime(\DateTimeInterface $booking_time): self
    {
        $this->booking_time = $booking_time;

        return $this;
    }
}
