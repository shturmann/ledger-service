<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTransactionRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly string $transactionId,

        #[Assert\NotBlank]
        #[Assert\Choice(['debit', 'credit'])]
        public readonly string $type,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly float $amount,

        #[Assert\NotBlank]
        #[Assert\Currency]
        public readonly string $currency
    ) {}
}