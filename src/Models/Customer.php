<?php
namespace BankingSystem\Models;

use BankingSystem\Abstracts\BaseModel;
use BankingSystem\Interfaces\ReportableInterface;
use BankingSystem\Models\Accounts\CheckingAccount;
use BankingSystem\Models\Accounts\SavingsAccount;

class Customer extends BaseModel implements ReportableInterface {
    private $name;
    private $accounts = [];
    
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
    
    public function openAccount($accountNumber, $initialBalance = 0, $type = 'checking') {
        switch (strtolower($type)) {
            case 'savings':
                $account = new SavingsAccount($accountNumber, $initialBalance);
                break;
            case 'checking':
            default:
                $account = new CheckingAccount($accountNumber, $initialBalance);
        }
        
        $this->accounts[$accountNumber] = $account;
        return $account;
    }
    
    public function getAccount($accountNumber) {
        if (!isset($this->accounts[$accountNumber])) {
            throw new \RuntimeException("Account not found");
        }
        return $this->accounts[$accountNumber];
    }
    
    public function getAccounts() {
        return $this->accounts;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function validate(): bool {
        return !empty($this->id) && !empty($this->name);
    }
    
    public function generateReport(): array {
        return [
            'customer_id' => $this->id,
            'name' => $this->name,
            'total_balance' => $this->getTotalBalance(),
            'account_count' => count($this->accounts),
            'accounts' => array_map(function($account) {
                return $account instanceof ReportableInterface 
                    ? $account->generateReport() 
                    : ['error' => 'Account does not implement ReportableInterface'];
            }, $this->accounts)
        ];
    }
    
    public function __toString() {
        return "Customer {$this->id}: {$this->name}";
    }
    
    public function __invoke($accountNumber) {
        return $this->getAccount($accountNumber);
    }
    
    public function __isset($name) {
        return isset($this->accounts[$name]);
    }
    
    public function __unset($name) {
        if (isset($this->accounts[$name])) {
            if ($this->accounts[$name]->getBalance() > 0) {
                throw new \RuntimeException("Cannot close account with positive balance");
            }
            unset($this->accounts[$name]);
        }
    }
    
    public function getAccountNumbers() {
        return array_map(function($account) {
            return $account->getAccountNumber();
        }, $this->accounts);
    }
    
    public function getActiveAccounts() {
        return array_filter($this->accounts, function($account) {
            return count($account->getTransactions()) > 0;
        });
    }
    
    public function getTotalBalance() {
        return array_reduce($this->accounts, function($sum, $account) {
            return $sum + $account->getBalance();
        }, 0);
    }
}