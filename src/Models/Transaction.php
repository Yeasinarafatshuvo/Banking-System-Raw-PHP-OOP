<?php
namespace BankingSystem\Models;

use BankingSystem\Interfaces\TransactionInterface;
use BankingSystem\Exceptions\TransactionException;

class Transaction implements TransactionInterface {
    private $type;
    private $amount;
    private $timestamp;
    private const ALLOWED_TYPES = ['deposit', 'withdrawal', 'transfer', 'fee'];

    public function __construct(string $type, float $amount) {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new TransactionException(
                "Invalid Transaction Type: {$type}",
                TransactionException::INVALID_TRANSACTION_TYPE,
                ['allowed_types' => self::ALLOWED_TYPES]
            );
        }

        if ($amount <= 0) {
            throw new TransactionException(
                "Transaction amount must be positive",
                TransactionException::INVALID_TRANSACTION_AMOUNT,
                ['amount' => $amount]
            );
        }

        $this->type = $type;
        $this->amount = $amount;
        $this->timestamp = new \DateTime();
    }

    public function getType(): string {
        return $this->type;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function getTimestamp(): \DateTimeInterface {
        return $this->timestamp;
    }

    public function __toString(): string {
        return ucfirst($this->type) . " of {$this->amount} at " . 
               $this->timestamp->format('Y-m-d H:i:s');
    }

}