<?php

namespace App\Entity;

use App\Repository\CurrentBalanceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CurrentBalanceRepository::class)
 */
class CurrentBalance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $balance;

    /**
     * @ORM\OneToOne(targetEntity=SubAccount::class, cascade={"persist", "remove"}, inversedBy="currentBalance")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subAccount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormattedBalance(): string
    {
        $formatter = new \NumberFormatter('de_DE', \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($this->getBalance(), 'EUR');
    }

    public function getBalance(): ?float
    {
        return $this->balance / 100;
    }

    public function getSubAccount(): ?SubAccount
    {
        return $this->subAccount;
    }

    public function setSubAccount(SubAccount $subAccount): self
    {
        $this->subAccount = $subAccount;

        return $this;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }
}
