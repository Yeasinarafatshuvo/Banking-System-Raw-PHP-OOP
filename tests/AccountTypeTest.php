<?php
namespace BankingSystem\Tests;

use BankingSystem\Models\Accounts\SavingsAccount;
use BankingSystem\Models\Accounts\CheckingAccount;
use PHPUnit\Framework\TestCase;

class AccountTypeTest extends TestCase {
    public function testSavingsAccountInterest() {
        $account = new SavingsAccount('S12345', 1000);
        $interest = $account->calculateInterest();
        $this->assertEquals(1.67, round($interest, 2));
    }
    
    public function testCheckingAccountFee() {
        $account = new CheckingAccount('C12345', 100);
        $initialBalance = $account->getBalance();
        $account->applyMonthlyFee();
        $this->assertEquals(95, $account->getBalance());
    }
    
    public function testSavingsAccountReport() {
        $account = new SavingsAccount('S12345', 1000);
        $report = $account->generateReport();
        
        $this->assertEquals('savings', $report['account_type']);
        $this->assertEquals(0.02, $report['interest_rate']);
        $this->assertArrayHasKey('projected_interest', $report);
    }
    
    public function testCheckingAccountReport() {
        $account = new CheckingAccount('C12345', 100);
        $report = $account->generateReport();
        
        $this->assertEquals('checking', $report['account_type']);
        $this->assertEquals(5.0, $report['monthly_fee']);
    }
    
    public function testInheritance() {
        $savings = new SavingsAccount('S12345', 1000);
        $this->assertInstanceOf(\BankingSystem\Abstracts\BankAccount::class, $savings);
        $this->assertInstanceOf(\BankingSystem\Models\Account::class, $savings);
        
        $checking = new CheckingAccount('C12345', 100);
        $this->assertInstanceOf(\BankingSystem\Abstracts\BankAccount::class, $checking);
        $this->assertInstanceOf(\BankingSystem\Models\Account::class, $checking);
    }
}