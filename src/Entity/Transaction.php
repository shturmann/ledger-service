<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Table(name: "transaction")]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Ledger::class, inversedBy: "transactions")]
    #[ORM\JoinColumn(nullable: false)]
    private Ledger $ledger;

    #[ORM\Column(type: "string", length: 36, unique: true)]
    private string $transactionId;

    #[ORM\Column(type: "string", length: 3)]
    private string $currency;

    #[ORM\Column(type: "decimal", precision: 20, scale: 4)]
    private float $amount;

    #[ORM\Column(type: "string", length: 10)]
    private string $type;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $created_at;

    public function __construct(Ledger $ledger, string $transactionId, string $type, float $amount, string $currency)
    {
        $this->ledger = $ledger;
        $this->transactionId = $transactionId;
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->created_at = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLedger(): Ledger
    {
        return $this->ledger;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;
        return $this;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
}