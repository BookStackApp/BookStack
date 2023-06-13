<?php

namespace BookStack\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class PrettyException extends Exception implements Responsable, HttpExceptionInterface
{
    /**
     * @var ?string
     */
    protected $subtitle = null;

    /**
     * @var ?string
     */
    protected $details = null;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Render a response for when this exception occurs.
     *
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        $code = $this->getStatusCode();

        return response()->view('errors.' . $code, [
            'message'  => $this->getMessage(),
            'subtitle' => $this->subtitle,
            'details'  => $this->details,
        ], $code);
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get the desired HTTP status code for this exception.
     */
    public function getStatusCode(): int
    {
        return ($this->getCode() === 0) ? 500 : $this->getCode();
    }

    /**
     * Get the desired HTTP headers for this exception.
     * @return array<mixed>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set the desired HTTP headers for this exception.
     * @param array<mixed> $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
}
