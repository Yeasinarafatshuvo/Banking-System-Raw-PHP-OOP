<?php
require_once __DIR__ . '/../src/bootstrap.php';

use BankingSystem\Models\Bank;
use BankingSystem\Models\Customer;
use BankingSystem\Services\BankingService;

try {
    // Create bank
    $bank = new Bank('CLI Bank');
    
    // Add customer
    $customer = new Customer('C2001', 'Bob Smith');
    $bank->addCustomer($customer);

    // Open accounts
    $savings = $customer->openAccount('S2001', 1000, 'savings');
    $checking = $customer->openAccount('C2001', 500, 'checking');
    
    // Create banking service
    $bankingService = new BankingService($bank);
    
    // Perform operations
    $savings->deposit(300);
    $checking->withdraw(100);
    $bankingService->transferFunds($savings, $checking, 200);
    
    // Apply monthly operations
    $bankingService->applyMonthlyOperations();
    
    // Generate report
    $report = $bankingService->generateBankReport();
    
    // Display results
    echo "Bank: {$bank->getName()}\n";
    echo "Customer: {$customer->getName()}\n";
    echo "Savings Balance: {$savings->getBalance()}\n";
    echo "Checking Balance: {$checking->getBalance()}\n";
    echo "\nFull Report:\n";
    echo json_encode($report, JSON_PRETTY_PRINT);

} catch (\Throwable $e) {
    fwrite(STDERR, "ERROR: " . $e->getMessage() . "\n");
    exit(1);
}