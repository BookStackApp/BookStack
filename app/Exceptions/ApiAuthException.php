<?php

namespace BookStack\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiAuthException extends \Exception implements HttpExceptionInterface
{
    protected int $status;

    /**
     * @var array<mixed>
     */
    protected array $headers = [];

    /**
     * ApiAuthException constructor.
     */
    public function __construct(string $message, int $statusCode = 401)
    {
        $this->status = $statusCode;
        parent::__construct($message, $statusCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
