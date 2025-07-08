<?php
namespace BankingSystem\Exceptions;

class TransactionException extends BankingSystemException {
    public const INVALID_TRANSACTION_TYPE = 2001;
    public const TRANSACTION_LIMIT_EXCEEDED = 2002;
    public const FRAUD_SUSPECTED = 2003;
    public const INVALID_TRANSACTION_AMOUNT = 1003;
}