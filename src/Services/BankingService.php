<?php
namespace BankingSystem\Services;

use BankingSystem\Abstracts\BankAccount;
use BankingSystem\Models\{Bank, Customer, Account};
use BankingSystem\Interfaces\AccountOperationsInterface;
use BankingSystem\Models\Transaction;

class BankingService {
    private $bank;
    
    public function __construct(Bank $bank) {
        $this->bank = $bank;
    }
    
    public function transferFunds(
        AccountOperationsInterface $fromAccount,
        AccountOperationsInterface $toAccount,
        float $amount
    ): bool {
        try {
            $fromAccount->withdraw($amount);
            $toAccount->deposit($amount);
            
            if ($fromAccount instanceof Account) {
                $fromAccount->getTransactions()[] = new Transaction('transfer', $amount);
            }
            
            if ($toAccount instanceof Account) {
                $toAccount->getTransactions()[] = new Transaction('transfer', $amount);
            }
            
            return true;
        } catch (\Exception $e) {
            // Log the error
            error_log("Transfer failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function applyMonthlyOperations(): void {
        foreach ($this->bank->getCustomers() as $customer) {
            foreach ($customer->getAccounts() as $account) {
                if ($account instanceof BankAccount) {
                    $account->applyMonthlyFee();
                    $interest = $account->calculateInterest();
                    if ($interest > 0) {
                        $account->deposit($interest);
                    }
                }
            }
        }
    }
    
    public function generateBankReport(): array {
        return $this->bank->generateReport();
    }
}