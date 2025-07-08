<?php
namespace BankingSystem\Tests;

use BankingSystem\Models\Transaction;
use BankingSystem\Exceptions\TransactionException;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase {
    public function testValidTransaction() {
        $transaction = new Transaction('deposit', 100);
        $this->assertEquals('deposit', $transaction->getType());
        $this->assertEquals(100, $transaction->getAmount());
        $this->assertInstanceOf(\DateTimeInterface::class, $transaction->getTimestamp());
    }
    
    public function testInvalidTransactionType() {
        $this->expectException(TransactionException::class);
        $this->expectExceptionCode(TransactionException::INVALID_TRANSACTION_TYPE);
        new Transaction('invalid_type', 100);
    }
    
    public function testInvalidTransactionAmount() {
        $this->expectException(TransactionException::class);
        $this->expectExceptionCode(TransactionException::INVALID_TRANSACTION_AMOUNT);
        new Transaction('deposit', -100);
    }
    
    public function testTransactionToString() {
        $transaction = new Transaction('withdrawal', 50);
        $this->assertStringStartsWith('Withdrawal of 50 at', (string)$transaction);
    }
    
    public function testSerialization() {
        $transaction = new Transaction('deposit', 100);
        $serialized = serialize($transaction);
        $unserialized = unserialize($serialized);
        
        $this->assertEquals($transaction->getType(), $unserialized->getType());
        $this->assertEquals($transaction->getAmount(), $unserialized->getAmount());
        $this->assertEquals(
            $transaction->getTimestamp()->format('Y-m-d H:i:s'),
            $unserialized->getTimestamp()->format('Y-m-d H:i:s')
        );
    }
    
    public function testAllowedTypes() {
        $reflection = new \ReflectionClass(Transaction::class);
        $constants = $reflection->getConstants();
        $allowedTypes = $constants['ALLOWED_TYPES'];

        $this->assertContains('deposit', $allowedTypes);
        $this->assertContains('withdrawal', $allowedTypes);
        $this->assertContains('transfer', $allowedTypes);
        $this->assertContains('fee', $allowedTypes);
    }
}