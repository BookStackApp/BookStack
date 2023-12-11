<?php

namespace BookStack\Http;

use GuzzleHttp\Psr7\Request as GuzzleRequest;

class HttpClientHistory
{
    public function __construct(
        protected &$container
    ) {
    }

    public function requestCount(): int
    {
        return count($this->container);
    }

    public function requestAt(int $index): ?GuzzleRequest
    {
        return $this->container[$index]['request'] ?? null;
    }

    public function latestRequest(): ?GuzzleRequest
    {
        return $this->requestAt($this->requestCount() - 1);
    }

    public function all(): array
    {
        return $this->container;
    }
}
