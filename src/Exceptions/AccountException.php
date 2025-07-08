<?php
namespace BankingSystem\Exceptions;

class AccountException extends BankingSystemException {
    public const INVALID_ACCOUNT_NUMBER = 1001;
    public const INSUFFICIENT_FUNDS = 1002;
    public const INVALID_TRANSACTION_AMOUNT = 1003;
    public const ACCOUNT_NOT_FOUND = 1004;
    public const TRANSACTION_FAILED = 1005;
    public const ACCOUNT_CLOSURE_WITH_BALANCE = 1006;
    public const ACCOUNT_INACTIVE = 1007;
}