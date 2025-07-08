<?php
namespace BankingSystem\Tests;

use BankingSystem\Models\Bank;
use BankingSystem\Models\Customer;
use PHPUnit\Framework\TestCase;

class BankTest extends TestCase {
    private $bank;
    
    protected function setUp(): void {
        $this->bank = new Bank('MyBank');
        
        $customer1 = new Customer('C1001', 'John Doe');
        $customer1->openAccount('A1001', 1000);
        $customer1->openAccount('A1002', 2000);
        
        $customer2 = new Customer('C1002', 'Jane Smith');
        $customer2->openAccount('A2001', 5000);
        
        $this->bank->addCustomer($customer1);
        $this->bank->addCustomer($customer2);
    }
    
    public function testAddCustomer() {
        $this->assertCount(2, $this->bank->getCustomers());
    }
    
    public function testGetCustomer() {
        $customer = $this->bank->getCustomer('C1001');
        $this->assertEquals('John Doe', $customer->getName());
    }
    
    public function testClone() {
        $bankCopy = clone $this->bank;
        $bankCopy->getCustomer('C1001')->openAccount('A1003', 3000);
        
        $this->assertCount(2, $this->bank->getCustomer('C1001')->getAccounts());
        $this->assertCount(3, $bankCopy->getCustomer('C1001')->getAccounts());
    }
    
    public function testArrayFunctions() {
        $names = $this->bank->getCustomerNames();
        $this->assertContains('John Doe', $names);
        $this->assertContains('Jane Smith', $names);
        
        $this->assertCount(1, $this->bank->getPremiumCustomers(3001));
        
        $premiumCustomers = $this->bank->getPremiumCustomers(3001);
        $this->assertEquals('Jane Smith', $premiumCustomers['C1002']->getName());
        
        $this->assertEquals(8000, $this->bank->getTotalDeposits());
    }
    
    public function testGenerateReport() {
        $report = $this->bank->generateReport();
        $this->assertEquals('MyBank', $report['bank_name']);
        $this->assertEquals(2, $report['customer_count']);
        $this->assertEquals(8000, $report['total_deposits']);
        $this->assertCount(2, $report['customers']);
    }
    
    public function testBankValidation() {
        $this->assertTrue($this->bank->validate());
        $invalidBank = new Bank('AB');
        $this->assertFalse($invalidBank->validate());
    }
}