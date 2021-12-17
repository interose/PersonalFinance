<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SplitTransactionRepository")
 */
class SplitTransaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Transaction", inversedBy="splitTransactions")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $transaction;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="splitTransaction")
     */
    private $category;

    /**
     * @ORM\Column(type="date")
     */
    private $valutaDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function hasCategory(): bool
    {
        return $this->category !== null;
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
