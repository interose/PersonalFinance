<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 */
class Account
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(groups={"new", "edit"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank(groups={"new", "edit"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $accountHolder;

    /**
     * @Assert\NotBlank(groups={"new", "edit"})
     * @Assert\Iban(groups={"new", "edit"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $iban;

    /**
     * @Assert\NotBlank(groups={"new", "edit"})
     * @Assert\Bic(groups={"new", "edit"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $bic;

    /**
     * @Assert\NotBlank(groups={"new", "edit"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $bankCode;

    /**
     * @Assert\NotBlank(groups={"new", "edit"})
     * @Assert\Url(groups={"new", "edit"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @Assert\NotBlank(groups={"new", "edit_credentials"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tanMediaName;

    /**
     * @Assert\NotBlank(groups={"new", "edit_credentials"})
     * @Assert\Positive(groups={"new", "edit_credentials"})
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tanMechanism;

    /**
     * @Assert\NotBlank(groups={"new", "edit_credentials"})
     *
     * @ORM\Column(type="binary", nullable=true)
     */
    private $username;

    /**
     * @Assert\NotBlank(groups={"new", "edit_credentials"})
     *
     * @ORM\Column(type="binary", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundColor;

    /**
     * @ORM\OneToMany(targetEntity=SubAccount::class, mappedBy="account", orphanRemoval=true)
     */
    private $subAccounts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foregroundColor;

    public function __construct()
    {
        $this->subAccounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAccountHolder(): ?string
    {
        return $this->accountHolder;
    }

    public function setAccountHolder(string $accountHolder): self
    {
        $this->accountHolder = $accountHolder;

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

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): self
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTanMediaName(): ?string
    {
        return $this->tanMediaName;
    }

    public function setTanMediaName(string $tanMediaName): self
    {
        $this->tanMediaName = $tanMediaName;

        return $this;
    }

    public function getTanMechanism(): ?int
    {
        return $this->tanMechanism;
    }

    public function setTanMechanism(int $tanMechanism): self
    {
        $this->tanMechanism = $tanMechanism;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * @return Collection|SubAccount[]
     */
    public function getSubAccounts(): Collection
    {
        return $this->subAccounts;
    }

    public function addSubAccount(SubAccount $subAccount): self
    {
        if (!$this->subAccounts->contains($subAccount)) {
            $this->subAccounts[] = $subAccount;
            $subAccount->setAccount($this);
        }

        return $this;
    }

    public function removeSubAccount(SubAccount $subAccount): self
    {
        if ($this->subAccounts->removeElement($subAccount)) {
            // set the owning side to null (unless already changed)
            if ($subAccount->getAccount() === $this) {
                $subAccount->setAccount(null);
            }
        }

        return $this;
    }

    public function getForegroundColor(): ?string
    {
        return $this->foregroundColor;
    }

    public function setForegroundColor(?string $foregroundColor): self
    {
        $this->foregroundColor = $foregroundColor;

        return $this;
    }
}
