<?php

namespace BookStack\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiAuthException extends \Exception implements HttpExceptionInterface, ShowsApiExceptionMessage
{
    protected int $status;

    public function __construct(string $message, int $statusCode = 401)
    {
        $this->status = $statusCode;
        parent::__construct($message, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getMessageForApi(): string
    {
        return $this->getMessage();
    }
}
