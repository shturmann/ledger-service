<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LedgerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LedgerRepository::class)]
#[ORM\Table(name: "ledger")]
class Ledger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 36, unique: true)]
    private string $identifier;

    #[ORM\Column(type: "string", length: 3)]
    private string $defaultCurrency;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $updatedAt;

    #[ORM\OneToMany(mappedBy: "ledger", targetEntity: Balance::class, cascade: ["persist", "remove"])]
    private Collection $balances;

    #[ORM\OneToMany(mappedBy: "ledger", targetEntity: Transaction::class, cascade: ["persist", "remove"])]
    private Collection $transactions;

    public function __construct(string $identifier, string $defaultCurrency)
    {
        $this->identifier = $identifier;
        $this->defaultCurrency = $defaultCurrency;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->balances = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getDefaultCurrency(): string
    {
        return $this->defaultCurrency;
    }

    public function setDefaultCurrency(string $defaultCurrency): self
    {
        $this->defaultCurrency = $defaultCurrency;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}