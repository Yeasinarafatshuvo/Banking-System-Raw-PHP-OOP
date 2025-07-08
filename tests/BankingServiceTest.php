<?php
namespace BankingSystem\Tests;

use BankingSystem\Models\{Bank, Customer};
use BankingSystem\Models\Accounts\{SavingsAccount, CheckingAccount};
use BankingSystem\Services\BankingService;
use PHPUnit\Framework\TestCase;

class BankingServiceTest extends TestCase {
    private $bank;
    private $service;
    
    protected function setUp(): void {
        $this->bank = new Bank('Test Bank');
        $this->service = new BankingService($this->bank);
        
        $customer = new Customer('C1001', 'John Doe');
        $this->bank->addCustomer($customer);
        
        $customer->openAccount('S1001', 1000, 'savings');
        $customer->openAccount('C1001', 500, 'checking');
    }
    
    public function testTransferFunds() {
        $savings = $this->bank->getCustomer('C1001')->getAccount('S1001');
        $checking = $this->bank->getCustomer('C1001')->getAccount('C1001');
        
        $this->assertTrue($this->service->transferFunds($savings, $checking, 200));
        $this->assertEquals(800, $savings->getBalance());
        $this->assertEquals(700, $checking->getBalance());
    }
    
    public function testFailedTransfer() {
        $savings = $this->bank->getCustomer('C1001')->getAccount('S1001');
        $checking = $this->bank->getCustomer('C1001')->getAccount('C1001');
        
        $this->assertFalse($this->service->transferFunds($savings, $checking, 2000));
    }
    
    // public function testApplyMonthlyOperations() {
    //     $savings = $this->bank->getCustomer('C1001')->getAccount('S1001');
    //     $checking = $this->bank->getCustomer('C1001')->getAccount('C1001');
        
    //     $initialSavings = $savings->getBalance();
    //     $initialChecking = $checking->getBalance();
        
    //     $this->service->applyMonthlyOperations();
        
    //     $this->assertGreaterThan($initialSavings, $savings->getBalance());
    //     $this->assertLessThan($initialChecking, $checking->getBalance());
    // }
    
    public function testGenerateBankReport() {
        $report = $this->service->generateBankReport();
        $this->assertEquals('Test Bank', $report['bank_name']);
        $this->assertCount(1, $report['customers']);
        $this->assertEquals(1500, $report['total_deposits']);
    }
    
    public function testPolymorphicAccountHandling() {
        $savings = $this->bank->getCustomer('C1001')->getAccount('S1001');
        $checking = $this->bank->getCustomer('C1001')->getAccount('C1001');
        
        $this->assertInstanceOf(SavingsAccount::class, $savings);
        $this->assertInstanceOf(CheckingAccount::class, $checking);
        
        // Both should work with transferFunds despite being different types
        $this->assertTrue($this->service->transferFunds($savings, $checking, 100));
    }
}