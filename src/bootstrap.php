<?php
require_once __DIR__ . '/../vendor/autoload.php';

use BankingSystem\Handlers\ExceptionHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Initialize logger
$logger = new Logger('banking-system');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/error.log', Logger::ERROR));

// Set environment
$environment = getenv('APP_ENV') ?: 'production';

// Register exception handler
$exceptionHandler = new ExceptionHandler($logger, $environment);
set_exception_handler([$exceptionHandler, 'handle']);

// Register error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) use ($exceptionHandler) {
    $exceptionHandler->handle(new \ErrorException($errstr, $errno, 0, $errfile, $errline));
});

// Register shutdown function for fatal errors
register_shutdown_function(function() use ($exceptionHandler) {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $exceptionHandler->handle(new \ErrorException(
            $error['message'], $error['type'], 0, $error['file'], $error['line']
        ));
    }
});