<?php

namespace BookStack\Exceptions;

/**
 * Exception within BookStack\Uploads\HttpFetcher.
 */
class HttpFetchException extends PrettyException
{
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($previous) {
            $this->setDetails($previous->getMessage());
        }
    }

    public function getStatusCode(): int
    {
        return 500;
    }
}
