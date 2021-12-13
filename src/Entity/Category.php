<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CategoryGroup", inversedBy="categories")
     */
    private $categoryGroup;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="category")
     */
    private $transaction;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SplitTransaction", mappedBy="category")
     */
    private $splitTransaction;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $treeIgnore;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $dashboardIgnore;

    public function __construct()
    {
        $this->transaction = new ArrayCollection();
        $this->splitTransaction = new ArrayCollection();
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

    public function getCategoryGroup(): ?CategoryGroup
    {
        return $this->categoryGroup;
    }

    public function setCategoryGroup(?CategoryGroup $categoryGroup): self
    {
        $this->categoryGroup = $categoryGroup;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransaction(): Collection
    {
        return $this->transaction;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transaction->contains($transaction)) {
            $this->transaction[] = $transaction;
            $transaction->setCategory($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transaction->contains($transaction)) {
            $this->transaction->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getCategory() === $this) {
                $transaction->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SplitTransaction[]
     */
    public function getSplitTransaction(): Collection
    {
        return $this->splitTransaction;
    }

    public function addSplitTransaction(SplitTransaction $splitTransaction): self
    {
        if (!$this->splitTransaction->contains($splitTransaction)) {
            $this->splitTransaction[] = $splitTransaction;
            $splitTransaction->setCategory($this);
        }

        return $this;
    }

    public function removeSplitTransaction(SplitTransaction $splitTransaction): self
    {
        if ($this->splitTransaction->contains($splitTransaction)) {
            $this->splitTransaction->removeElement($splitTransaction);
            // set the owning side to null (unless already changed)
            if ($splitTransaction->getCategory() === $this) {
                $splitTransaction->setCategory(null);
            }
        }

        return $this;
    }

    public function getTreeIgnore(): ?bool
    {
        return $this->treeIgnore;
    }

    public function setTreeIgnore(?bool $treeIgnore): self
    {
        $this->treeIgnore = $treeIgnore;

        return $this;
    }

    public function getDashboardIgnore(): ?bool
    {
        return $this->dashboardIgnore;
    }

    public function setDashboardIgnore(?bool $dashboardIgnore): self
    {
        $this->dashboardIgnore = $dashboardIgnore;

        return $this;
    }
}
