<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BalanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BalanceRepository::class)]
#[ORM\Table(name: "balance")]
class Balance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Ledger::class, inversedBy: "balances")]
    #[ORM\JoinColumn(nullable: false)]
    private Ledger $ledger;

    #[ORM\Column(type: "string", length: 3)]
    private string $currency;

    #[ORM\Column(type: "decimal", precision: 20, scale: 4)]
    private float $amount = 0.0;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $updatedAt;

    public function __construct(Ledger $ledger, string $currency)
    {
        $this->ledger = $ledger;
        $this->currency = $currency;
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLedger(): Ledger
    {
        return $this->ledger;
    }

    public function setLedger(Ledger $ledger): self
    {
        $this->ledger = $ledger;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
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