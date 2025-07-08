<?php
namespace BankingSystem\Interfaces;

interface ReportableInterface {
    public function generateReport(): array;
}