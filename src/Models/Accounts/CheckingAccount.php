<?php
namespace BankingSystem\Models\Accounts;

use BankingSystem\Abstracts\BankAccount;
use BankingSystem\Exceptions\AccountException;
use BankingSystem\Models\Transaction;

class CheckingAccount extends BankAccount {
    protected $monthlyFee = 5.00;
    
    public function calculateInterest(): float {
        return 0; // No interest for checking account
    }
    
    public function applyMonthlyFee(): void {
        try {
            $this->withdraw($this->monthlyFee);
            $this->getTransactions()[] = new Transaction('fee', $this->monthlyFee);
        } catch (AccountException $e) {
            // Handle insufficient funds for fee
            // Could implement overdraft protection here
        }
    }
    
    public function generateReport(): array {
        $report = parent::generateReport();
        $report['account_type'] = 'checking';
        $report['monthly_fee'] = $this->monthlyFee;
        return $report;
    }
}