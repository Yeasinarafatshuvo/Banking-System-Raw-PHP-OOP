<?php
namespace BankingSystem\Models;

use BankingSystem\Abstracts\BaseModel;
use BankingSystem\Interfaces\ReportableInterface;

class Bank extends BaseModel implements ReportableInterface  {
    private $name;
    private $customers = [];
    
    public function __construct($name) {
        $this->id = uniqid('bank_');
        $this->name = $name;
    }
    
    public function addCustomer(Customer $customer) {
        $this->customers[$customer->getId()] = $customer;
        return $this;
    }
    
    public function getCustomer($customerId) {
        if (!isset($this->customers[$customerId])) {
            throw new \RuntimeException("Customer not found");
        }
        return $this->customers[$customerId];
    }
    
    public function getCustomers() {
        return $this->customers;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function validate(): bool {
        return !empty($this->name) && strlen($this->name) >= 3;
    }
    
    public function generateReport(): array {
        return [
            'bank_name' => $this->name,
            'customer_count' => count($this->customers),
            'total_deposits' => $this->getTotalDeposits(),
            'premium_customers' => count($this->getPremiumCustomers(10000)),
            'customers' => array_map(function($customer) {
                return $customer instanceof ReportableInterface 
                    ? $customer->generateReport() 
                    : ['error' => 'Customer does not implement ReportableInterface'];
            }, $this->customers)
        ];
    }
    
    public function __clone() {
        $this->customers = array_map(function($customer) {
            return clone $customer;
        }, $this->customers);
    }
    
    public function getCustomerNames() {
        return array_map(function($customer) {
            return $customer->getName();
        }, $this->customers);
    }
    
    public function getPremiumCustomers($threshold) {
        return array_filter($this->customers, function($customer) use ($threshold) {
            return $customer->getTotalBalance() >= $threshold;
        });
    }
    
    public function getTotalDeposits() {
        return array_reduce($this->customers, function($sum, $customer) {
            return $sum + array_reduce($customer->getAccounts(), function($accSum, $account) {
                return $accSum + $account->getTotalDeposits();
            }, 0);
        }, 0);
    }
}