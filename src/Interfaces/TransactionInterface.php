<?php
namespace BankingSystem\Interfaces;

interface TransactionInterface {
    public function getType(): string;
    public function getAmount(): float;
    public function getTimestamp(): \DateTimeInterface;
}