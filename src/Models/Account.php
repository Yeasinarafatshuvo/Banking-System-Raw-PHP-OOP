<?php
namespace BankingSystem\Models;

use BankingSystem\Abstracts\BaseModel;
use BankingSystem\Interfaces\{AccountOperationsInterface, ReportableInterface};
use BankingSystem\Exceptions\AccountException;

class Account extends BaseModel implements AccountOperationsInterface, ReportableInterface {
    protected $accountNumber;
    protected $balance;
    protected $transactions = [];
    protected $isActive = true;
    
    public function __construct(string $accountNumber, float $initialBalance = 0) {
        $this->id = $accountNumber;
        $this->accountNumber = $accountNumber;
        $this->balance = $initialBalance;
        
        if ($initialBalance > 0) {
            $this->transactions[] = new Transaction('deposit', $initialBalance);
        }
    }
    
    public function deposit(float $amount): self {
        if (!$this->isActive) {
            throw new AccountException(
                "Account is inactive",
                AccountException::ACCOUNT_INACTIVE
            );
        }
        
        if ($amount <= 0) {
            throw new AccountException(
                "Deposit amount must be positive",
                AccountException::INVALID_TRANSACTION_AMOUNT
            );
        }
        
        $this->balance += $amount;
        $this->transactions[] = new Transaction('deposit', $amount);
        return $this;
    }
    
    public function withdraw(float $amount): self {
        if (!$this->isActive) {
            throw new AccountException(
                "Account is inactive",
                AccountException::ACCOUNT_INACTIVE
            );
        }
        
        if ($amount <= 0) {
            throw new AccountException(
                "Withdrawal amount must be positive",
                AccountException::INVALID_TRANSACTION_AMOUNT
            );
        }
        
        if ($this->balance < $amount) {
            throw new AccountException(
                "Insufficient funds",
                AccountException::INSUFFICIENT_FUNDS
            );
        }
        
        $this->balance -= $amount;
        $this->transactions[] = new Transaction('withdrawal', $amount);
        return $this;
    }
    
    public function getBalance(): float {
        return $this->balance;
    }
    
    public function getAccountNumber(): string {
        return $this->accountNumber;
    }
    
    public function getTransactions(): array {
        return $this->transactions;
    }
    
    public function close(): void {
        if ($this->balance != 0) {
            throw new AccountException(
                "Cannot close account with non-zero balance",
                AccountException::ACCOUNT_CLOSURE_WITH_BALANCE
            );
        }
        $this->isActive = false;
    }
    
    public function isActive(): bool {
        return $this->isActive;
    }
    
    public function validate(): bool {
        return !empty($this->accountNumber) && preg_match('/^[A-Z0-9]{6,12}$/', $this->accountNumber);
    }
    
    public function generateReport(): array {
        return [
            'account_number' => $this->accountNumber,
            'balance' => $this->balance,
            'is_active' => $this->isActive,
            'total_transactions' => count($this->transactions),
            'last_transaction' => $this->lastTransaction ? $this->lastTransaction->getTimestamp()->format('Y-m-d H:i:s') : null
        ];
    }
    
    public function getTransactionsByType(string $type): array {
        return array_values(array_filter($this->transactions, function($transaction) use ($type) {
            return $transaction instanceof Transaction 
                && $transaction->getType() === $type;
        }));
    }
    
    public function getTotalDeposits(): float {
        return array_reduce($this->transactions, function($sum, $transaction) {
            return $transaction->getType() === 'deposit' ? $sum + $transaction->getAmount() : $sum;
        }, 0);
    }
    
    public function getTotalWithdrawals(): float {
        return array_reduce($this->transactions, function($sum, $transaction) {
            return $transaction->getType() === 'withdrawal' ? $sum + $transaction->getAmount() : $sum;
        }, 0);
    }
    
    public function __toString(): string {
        return "Account #{$this->accountNumber} - Balance: {$this->balance}";
    }
    
    public function __get($name) {
        if ($name === 'lastTransaction') {
            return end($this->transactions) ?: null;
        }
        throw new \RuntimeException("Property {$name} does not exist");
    }
    
    public function __call($name, $arguments) {
        if (strpos($name, 'getTransactionsBy') === 0) {
            $type = strtolower(substr($name, strlen('getTransactionsBy')));
            return $this->getTransactionsByType($type);
        }
        throw new \RuntimeException("Method {$name} does not exist");
    }
}