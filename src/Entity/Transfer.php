<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransferRepository")
 */
class Transfer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Der Verwendungszweck darf nicht leer sein"
     * )
     * @Assert\Length(
     *     min = 5,
     *     max = 250,
     *     minMessage="Der Verwendungszweck muss mindestens {{ limit }} Zeichen lang sein",
     *     maxMessage="Der Verwendungszweck darf maximal {{ limit }} Zeichen lang sein"
     * )
     */
    private $info;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Der Empfänger darf nicht leer sein!"
     * )
     * @Assert\Length(
     *     min = 5,
     *     max = 250,
     *     minMessage="Der Empfänger muss mindestens {{ limit }} Zeichen lang sein",
     *     maxMessage="Der Empfänger darf maximal {{ limit }} Zeichen lang sein"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Die IBAN darf nicht leer sein"
     * )
     * @Assert\Iban(
     *     message="Bitte eine gültige IBAN eingeben!"
     * )
     */
    private $iban;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Die BIC darf nicht leer sein"
     * )
     * @Assert\Bic(
     *     message="Bitte eine gültige BIC eingeben"
     * )
     */
    private $bic;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Der Bankname darf nicht leer sein"
     * )
     */
    private $bankName;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(
     *     message="Der Betrag darf nicht leer sein"
     * )
     * @Assert\GreaterThan(
     *     value=0,
     *     message="Bitte einen höheren Wert als {{ value }} Euro angeben"
     * )
     */
    private $amount;

    /**
     * @ORM\Column(type="date")
     */
    private $executionDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(string $info): self
    {
        $this->info = $info;

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

    public function getExecutionDate(): ?\DateTimeInterface
    {
        return $this->executionDate;
    }

    public function setExecutionDate(\DateTimeInterface $executionDate): self
    {
        $this->executionDate = $executionDate;

        return $this;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): self
    {
        $this->bankName = $bankName;

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
