<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreateLedgerRequest;
use App\DTO\CreateTransactionRequest;
use App\Entity\Balance;
use App\Entity\Ledger;
use App\Entity\Transaction;
use App\Exception\LedgerNotFoundException;
use App\Repository\LedgerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class LedgerService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LedgerRepository $ledgerRepository
    ) {}

    public function createLedger(CreateLedgerRequest $request): Ledger
    {
        $this->entityManager->beginTransaction();

        try {
            $ledger = new Ledger(
                Uuid::v4()->toRfc4122(),
                $request->currency
            );

            $balance = new Balance($ledger, $request->currency);

            $this->entityManager->persist($ledger);
            $this->entityManager->persist($balance);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $ledger;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function createTransaction(
        string $identifier,
        CreateTransactionRequest $request
    ): Transaction {
        $this->entityManager->beginTransaction();

        try {
            $ledger = $this->ledgerRepository->findOneByIdentifier($identifier);

            if (!$ledger) {
                throw new LedgerNotFoundException();
            }

            $transaction = new Transaction(
                $ledger,
                $request->transactionId,
                $request->type,
                $request->amount,
                $request->currency
            );

            $balance = $this->entityManager->getRepository(Balance::class)
                ->findOneBy(['ledger' => $ledger, 'currency' => $request->currency]);

            if (!$balance) {
                $balance = new Balance($ledger, $request->currency);
                $this->entityManager->persist($balance);
            }

            $newAmount = $request->type === 'credit'
                ? $balance->getAmount() + $request->amount
                : $balance->getAmount() - $request->amount;

            $balance->setAmount($newAmount);

            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $transaction;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function getBalances(string $identifier): array
    {
        $ledger = $this->ledgerRepository->findOneByIdentifier($identifier);

        if (!$ledger) {
            throw new LedgerNotFoundException();
        }

        $balances = $this->entityManager->getRepository(Balance::class)
            ->findBy(['ledger' => $ledger]);

        $result = [];
        foreach ($balances as $balance) {
            $result[$balance->getCurrency()] = [
                'amount' => $balance->getAmount(),
                'updated_at' => $balance->getUpdatedAt()->format('Y-m-d\TH:i:s\Z')
            ];
        }

        return $result;
    }
}