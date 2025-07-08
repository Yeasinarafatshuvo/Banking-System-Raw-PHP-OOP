<?php
namespace BankingSystem\Interfaces;

interface AccountOperationsInterface {
    public function deposit(float $amount): self;
    public function withdraw(float $amount): self;
    public function getBalance(): float;
}