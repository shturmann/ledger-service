<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateLedgerRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Currency]
        public readonly string $currency
    ) {}
}
