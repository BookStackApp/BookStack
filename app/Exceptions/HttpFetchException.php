<?php

namespace BookStack\Exceptions;

class HttpFetchException extends PrettyException
{
    /**
     * Construct exception within BookStack\Uploads\HttpFetcher.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
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
