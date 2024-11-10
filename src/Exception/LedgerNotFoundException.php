<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LedgerNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('Ledger not found');
    }
}