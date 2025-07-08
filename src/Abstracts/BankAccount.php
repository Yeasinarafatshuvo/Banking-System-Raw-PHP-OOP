<?php
namespace BankingSystem\Abstracts;

use BankingSystem\Models\Account;
use BankingSystem\Interfaces\AccountOperationsInterface;

abstract class BankAccount extends Account {
    protected $interestRate = 0;
    protected $monthlyFee = 0;
    
    public function __construct(string $accountNumber, float $initialBalance = 0) {
        parent::__construct($accountNumber, $initialBalance);
    }
    
    abstract public function calculateInterest(): float;
    abstract public function applyMonthlyFee(): void;
    
    public function getInterestRate(): float {
        return $this->interestRate;
    }
    
    public function getMonthlyFee(): float {
        return $this->monthlyFee;
    }
}