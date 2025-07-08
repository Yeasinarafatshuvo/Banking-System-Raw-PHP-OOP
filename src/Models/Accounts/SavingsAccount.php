<?php
namespace BankingSystem\Models\Accounts;

use BankingSystem\Abstracts\BankAccount;


class SavingsAccount extends BankAccount {
    protected $interestRate = 0.02; // 2% annual interest
    
    public function calculateInterest(): float {
        return $this->getBalance() * ($this->interestRate / 12); // Monthly interest
    }
    
    public function applyMonthlyFee(): void {
        // No monthly fee for savings account
    }
    
    public function generateReport(): array {
        $report = parent::generateReport();
        $report['account_type'] = 'savings';
        $report['interest_rate'] = $this->interestRate;
        $report['projected_interest'] = $this->calculateInterest();
        return $report;
    }
}