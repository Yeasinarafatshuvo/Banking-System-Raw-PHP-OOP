<?php
namespace BankingSystem\Exceptions;

class CustomerException extends BankingSystemException {
    public const CUSTOMER_NOT_FOUND = 3001;
    public const INVALID_CUSTOMER_DATA = 3002;
    public const CUSTOMER_VERIFICATION_FAILED = 3003;
}