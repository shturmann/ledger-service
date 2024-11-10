<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\LedgerService;
use App\DTO\CreateLedgerRequest;
use App\DTO\CreateTransactionRequest;
use App\Entity\Ledger;
use App\Entity\Balance;
use App\Entity\Transaction;
use App\Repository\LedgerRepository;
use App\Exception\LedgerNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class LedgerServiceTest extends TestCase
{
    /** @var LedgerService */
    private $ledgerService;

    /** @var MockObject|EntityManagerInterface */
    private $entityManagerMock;

    /** @var MockObject|LedgerRepository */
    private $ledgerRepositoryMock;

    protected function setUp(): void
    {
        // Create mocks for dependencies
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->ledgerRepositoryMock = $this->createMock(LedgerRepository::class);

        // Create the LedgerService with mocked dependencies
        $this->ledgerService = new LedgerService($this->entityManagerMock, $this->ledgerRepositoryMock);
    }

    public function testCreateLedger_Success()
    {
        // Create the request object
        $createLedgerRequest = new CreateLedgerRequest('USD');
    
        // Mock Ledger and Balance entities
        $ledgerMock = $this->createMock(Ledger::class);
        $balanceMock = $this->createMock(Balance::class);
    
        // Mock EntityManager methods
        $this->entityManagerMock->expects($this->once())
            ->method('beginTransaction');
    
        // Expect the first persist call with Ledger object
        $this->entityManagerMock->expects($this->at(1))
            ->method('persist')
            ->with($this->isInstanceOf(Ledger::class));
    
        // Expect the second persist call with Balance object
        $this->entityManagerMock->expects($this->at(2))
            ->method('persist')
            ->with($this->isInstanceOf(Balance::class));
    
        // Expect flush and commit to be called once
        $this->entityManagerMock->expects($this->once())
            ->method('flush');
        $this->entityManagerMock->expects($this->once())
            ->method('commit');
    
        // Call the method to test
        $ledger = $this->ledgerService->createLedger($createLedgerRequest);
    
        // Assert the returned ledger is of the correct type
        $this->assertInstanceOf(Ledger::class, $ledger);
    }

    public function testCreateTransaction_Success()
    {
        // Create the request object
        $createTransactionRequest = new CreateTransactionRequest('tx123', 'debit', 100.0, 'USD');

        // Mock Ledger and Balance entities
        $ledgerMock = $this->createMock(Ledger::class);
        $balanceMock = $this->createMock(Balance::class);

        // Mock the LedgerRepository findOneByIdentifier method
        $this->ledgerRepositoryMock->expects($this->once())
            ->method('findOneByIdentifier')
            ->with('some-uuid')
            ->willReturn($ledgerMock);

        // Mock the Balance repository
        $balanceRepositoryMock = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $balanceRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['ledger' => $ledgerMock, 'currency' => 'USD'])
            ->willReturn($balanceMock);  // Return the balanceMock when findOneBy is called

        // Mock the EntityManager to return the mocked Balance repository
        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(Balance::class)
            ->willReturn($balanceRepositoryMock);

        // Mock the Balance methods
        $balanceMock->expects($this->once())
            ->method('getAmount')
            ->willReturn(200.0);  // Initial balance amount
        $balanceMock->expects($this->once())
            ->method('setAmount')
            ->with(100.0);  // New balance after transaction

        // Mock EntityManager methods
        $this->entityManagerMock->expects($this->once())
            ->method('beginTransaction');
        $this->entityManagerMock->expects($this->at(2))
            ->method('persist')
            ->with($this->isInstanceOf(Transaction::class));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');
        $this->entityManagerMock->expects($this->once())
            ->method('commit');

        // Call the method to test
        $transaction = $this->ledgerService->createTransaction('some-uuid', $createTransactionRequest);

        // Assert that the returned transaction is of the correct type
        $this->assertInstanceOf(Transaction::class, $transaction);
    }

    public function testCreateTransaction_LedgerNotFoundException()
    {
        // Create the request object
        $createTransactionRequest = new CreateTransactionRequest('tx123', 'debit', 100.0, 'USD');

        // Mock LedgerRepository to return null (ledger not found)
        $this->ledgerRepositoryMock->expects($this->once())
            ->method('findOneByIdentifier')
            ->with('invalid-uuid')
            ->willReturn(null);

        // Expect LedgerNotFoundException to be thrown
        $this->expectException(LedgerNotFoundException::class);

        // Call the method to test
        $this->ledgerService->createTransaction('invalid-uuid', $createTransactionRequest);
    }

    public function testGetBalances_Success()
    {
        // Mock the Ledger object
        $ledgerMock = $this->createMock(Ledger::class);

        // Mock the Balance object
        $balanceMock = $this->createMock(Balance::class);
        $balanceMock->method('getCurrency')->willReturn('USD');
        $balanceMock->method('getAmount')->willReturn(1000.0);
        $balanceMock->method('getUpdatedAt')->willReturn(new \DateTimeImmutable());

        // Mock EntityRepository for Balance
        $balanceRepositoryMock = $this->createMock(\Doctrine\ORM\EntityRepository::class);
        $balanceRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with(['ledger' => $ledgerMock])
            ->willReturn([$balanceMock]);  // Return an array of balances

        // Mock EntityManager to return the mocked repository
        $this->entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with(Balance::class)
            ->willReturn($balanceRepositoryMock);

        // Mock LedgerRepository to return a Ledger
        $this->ledgerRepositoryMock->expects($this->once())
            ->method('findOneByIdentifier')
            ->with('some-uuid')
            ->willReturn($ledgerMock);

        // Call the method
        $balances = $this->ledgerService->getBalances('some-uuid');

        // Assert the returned balances are in the expected format
        $this->assertArrayHasKey('USD', $balances);  // Assert that the currency 'USD' exists
        $this->assertEquals(1000.0, $balances['USD']['amount']);  // Assert that the amount is correct
    }
}