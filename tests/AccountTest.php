<?php
namespace BankingSystem\Tests;

use BankingSystem\Exceptions\AccountException;
use BankingSystem\Models\Account;
use BankingSystem\Models\Transaction;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AccountTest extends TestCase {
    private $account;
    
    protected function setUp(): void {
        $this->account = new Account('123456', 1000);
    }
    
    public function testInitialBalance() {
        $this->assertEquals(1000, $this->account->getBalance());
        $this->assertCount(1, $this->account->getTransactions());
    }
    
    public function testDeposit() {
        $this->account->deposit(500);
        $this->assertEquals(1500, $this->account->getBalance());
        $this->assertCount(2, $this->account->getTransactions());
    }
    
    public function testWithdraw() {
        $this->account->withdraw(500);
        $this->assertEquals(500, $this->account->getBalance());
        $this->assertCount(2, $this->account->getTransactions());
    }
    
    public function testInsufficientFunds() {
        $this->expectException(RuntimeException::class);
        $this->account->withdraw(2000);
    }
    
    public function testInvalidDeposit() {
        $this->expectException(AccountException::class);
        $this->account->deposit(-100);
    }
    
    public function testMagicToString() {
        $this->assertStringContainsString('Account #123456', (string)$this->account);
        $this->assertStringContainsString('Balance: 1000', (string)$this->account);
    }
    
    public function testMagicGetLastTransaction() {
        $this->account->deposit(500);
        $lastTransaction = $this->account->lastTransaction;
        
        $this->assertInstanceOf(Transaction::class, $lastTransaction);
        $this->assertEquals('deposit', $lastTransaction->getType());
        $this->assertEquals(500, $lastTransaction->getAmount());
    }
    
    public function testTransactionFiltering() {
        $account = new Account('654321');
        $account->deposit(500);
        $account->deposit(300);
        $account->withdraw(200);
        
        $deposits = $account->getTransactionsByType('deposit');
        $this->assertCount(2, $deposits);
        
        $depositAmounts = array_map(fn($t) => $t->getAmount(), $deposits);
        $this->assertEqualsCanonicalizing([500, 300], $depositAmounts);
        
        $withdrawals = $account->getTransactionsByType('withdrawal');
        $this->assertCount(1, $withdrawals);
        $this->assertEquals(200, $withdrawals[0]->getAmount());
    }
    
    public function testArrayFunctions() {
        $this->account->deposit(500);
        $this->account->deposit(300);
        $this->account->withdraw(200);
        
        $this->assertEquals(1800, $this->account->getTotalDeposits());
        $this->assertEquals(200, $this->account->getTotalWithdrawals());
    }
    
    public function testZeroInitialBalance() {
        $account = new Account('654321');
        $this->assertEquals(0, $account->getBalance());
        $this->assertCount(0, $account->getTransactions());
    }
    
    public function testInvalidMagicCall() {
        $this->expectException(RuntimeException::class);
        $this->account->nonExistentMethod();
    }
    
    public function testInvalidMagicGet() {
        $this->expectException(RuntimeException::class);
        $invalid = $this->account->invalidProperty;
    }
    
    public function testAccountValidation() {
        $this->assertTrue($this->account->validate());
        $invalidAccount = new Account('', 100);
        $this->assertFalse($invalidAccount->validate());
    }
    
    public function testAccountClosure() {
        $account = new Account('A1001', 0);
        $account->close();
        $this->assertFalse($account->isActive());
        
        $this->expectException(\BankingSystem\Exceptions\AccountException::class);
        $this->account->close();
    }
}