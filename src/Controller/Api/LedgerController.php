<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\CreateLedgerRequest;
use App\DTO\CreateTransactionRequest;
use App\Service\LedgerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1')]
class LedgerController extends AbstractController
{
    public function __construct(
        private readonly LedgerService $ledgerService,
        private readonly ValidatorInterface $validator
    ) {}

    #[Route('/ledgers', methods: ['POST'])]
    #[OA\Post(
        path: '/api/v1/ledgers',
        summary: 'Create new ledger',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CreateLedgerRequest")
        ),
        responses: [
            new OA\Response(response: 201, description: 'Ledger created'),
            new OA\Response(response: 400, description: 'Invalid input')
        ]
    )]
    public function createLedger(
        #[MapRequestPayload] CreateLedgerRequest $request
    ): JsonResponse {
        $ledger = $this->ledgerService->createLedger($request);
        
        return new JsonResponse([
            'id' => $ledger->getIdentifier(),
            'currency' => $ledger->getDefaultCurrency(),
            'created_at' => $ledger->getCreatedAt()->format('Y-m-d\TH:i:s\Z')
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/ledgers/{identifier}/transactions', methods: ['POST'])]
    public function createTransaction(
        string $identifier,
        #[MapRequestPayload] CreateTransactionRequest $request
    ): JsonResponse {
        $transaction = $this->ledgerService->createTransaction($identifier, $request);
        
        return new JsonResponse([
            'id' => $transaction->getTransactionId(),
            'status' => 'completed',
            'amount' => $transaction->getAmount(),
            'currency' => $transaction->getCurrency(),
            'created_at' => $transaction->getCreatedAt()->format('Y-m-d\TH:i:s\Z')
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/ledgers/{identifier}/balance', methods: ['GET'])]
    public function getBalance(string $identifier): JsonResponse
    {
        $balances = $this->ledgerService->getBalances($identifier);
        
        return new JsonResponse([
            'ledger_id' => $identifier,
            'balances' => $balances
        ]);
    }
}