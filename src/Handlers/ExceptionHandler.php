<?php
namespace BankingSystem\Handlers;

use BankingSystem\Exceptions\BankingSystemException;
use Psr\Log\LoggerInterface;

class ExceptionHandler {
    private $logger;
    private $environment;

    public function __construct(LoggerInterface $logger, string $environment = 'production') {
        $this->logger = $logger;
        $this->environment = $environment;
    }

    public function handle(\Throwable $exception): void {
        $this->logException($exception);
        
        if ($exception instanceof BankingSystemException) {
            $this->handleBankingSystemException($exception);
        } else {
            $this->handleGenericException($exception);
        }
    }

    protected function handleBankingSystemException(BankingSystemException $exception): void {
        $response = [
            'error' => [
                'type' => (new \ReflectionClass($exception))->getShortName(),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'category' => $this->getExceptionCategory($exception)
            ]
        ];

        if ($this->environment === 'development') {
            $response['error']['context'] = $exception->getContext();
            $response['error']['trace'] = $exception->getTraceAsString();
        }

        $this->sendResponse($response, $this->determineHttpStatusCode($exception));
    }

    protected function handleGenericException(\Throwable $exception): void {
        $response = [
            'error' => [
                'type' => 'InternalServerError',
                'message' => 'An unexpected error occurred',
                'code' => 500
            ]
        ];

        if ($this->environment === 'development') {
            $response['error']['details'] = $exception->getMessage();
            $response['error']['trace'] = $exception->getTraceAsString();
        }

        $this->sendResponse($response, 500);
    }

    protected function determineHttpStatusCode(BankingSystemException $exception): int {
        $code = $exception->getCode();
        
        if ($code >= 1000 && $code < 2000) return 400;
        if ($code >= 2000 && $code < 3000) return 422;
        if ($code >= 3000 && $code < 4000) return 403;
        
        return 500;
    }

    protected function getExceptionCategory(BankingSystemException $exception): string {
        $class = get_class($exception);
        
        if (strpos($class, 'AccountException') !== false) {
            return 'account';
        } elseif (strpos($class, 'TransactionException') !== false) {
            return 'transaction';
        } elseif (strpos($class, 'CustomerException') !== false) {
            return 'customer';
        }
        
        return 'general';
    }

    protected function logException(\Throwable $exception): void {
        $context = [
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];

        if ($exception instanceof BankingSystemException) {
            $context = array_merge($context, $exception->getContext());
        }

        $this->logger->error($exception->getMessage(), $context);
    }

    protected function sendResponse(array $response, int $statusCode): void {
        if (php_sapi_name() === 'cli') {
            fwrite(STDERR, json_encode($response, JSON_PRETTY_PRINT));
            exit(1);
        }

        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($response);
        exit;
    }
}