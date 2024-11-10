<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LedgerControllerTest extends WebTestCase
{
    public function testCreateLedger(): void
    {
        // Simulate sending a POST request to /api/v1/ledgers
        $client = static::createClient();
        
        // Define the payload for creating a ledger
        $data = [
            'currency' => 'USD',
        ];

        // Perform the POST request to create a ledger
        $client->request('POST', '/api/v1/ledgers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        // Assert that the response status code is 201 (Created)
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Assert that the response JSON contains the expected structure
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('currency', $responseData);
        $this->assertArrayHasKey('created_at', $responseData);
        $this->assertEquals('USD', $responseData['currency']);
    }

    public function testCreateTransaction(): void
    {
        // First, create a ledger to associate the transaction with
        $client = static::createClient();
        
        // Define the payload for creating a ledger
        $data = ['currency' => 'USD'];
        $client->request('POST', '/api/v1/ledgers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        // Get the ledger ID from the response
        $ledgerData = json_decode($client->getResponse()->getContent(), true);
        $ledgerId = $ledgerData['id'];

        // Now create a transaction for the created ledger
        $transactionData = [
            'transactionId' => '550e8400-e29b-41d4-a716-446655440000',  // UUID for transactionId
            'type' => 'credit',
            'amount' => 100.50,
            'currency' => 'USD'
        ];

        // Perform the POST request to create a transaction
        $client->request('POST', "/api/v1/ledgers/{$ledgerId}/transactions", [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($transactionData));

        // Assert that the response status code is 201 (Created)
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Assert that the response JSON contains the expected structure
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertArrayHasKey('amount', $responseData);
        $this->assertEquals('USD', $responseData['currency']);
        $this->assertEquals(100.50, $responseData['amount']);
        $this->assertEquals('completed', $responseData['status']);
    }

    public function testGetBalance(): void
    {
        // First, create a ledger to associate the balance with
        $client = static::createClient();
        
        // Define the payload for creating a ledger
        $data = ['currency' => 'USD'];
        $client->request('POST', '/api/v1/ledgers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        // Get the ledger ID from the response
        $ledgerData = json_decode($client->getResponse()->getContent(), true);
        $ledgerId = $ledgerData['id'];

        // Now get the balance for the created ledger
        $client->request('GET', "/api/v1/ledgers/{$ledgerId}/balance");

        // Assert that the response status code is 200 (OK)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Assert that the response JSON contains the expected structure
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('ledger_id', $responseData);
        $this->assertArrayHasKey('balances', $responseData);
        $this->assertArrayHasKey('USD', $responseData['balances']);
        $this->assertArrayHasKey('amount', $responseData['balances']['USD']);
    }
}