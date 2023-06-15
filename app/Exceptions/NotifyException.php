<?php

namespace BookStack\Exceptions;

use Exception;
use Throwable;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Exception that is supposed flash the error notification to the user after redirecting to a specified page.
 */
class NotifyException extends Exception implements Responsable, HttpExceptionInterface
{
    public $message;
    public $redirectLocation;
    protected $status;

    /**
     * @var array<mixed>
     */
    protected array $headers = [];

    public function __construct(string $message, string $redirectLocation = '/', int $status = 500, ?Throwable $previous = null)
    {
        $this->redirectLocation = $redirectLocation;
        $this->status = $status;

        if ($status >= 300 && $status < 400) {
            // add redirect header only when a matching HTTP status is given
            $this->headers = ['location' => $redirectLocation];
        }

        parent::__construct($message, $status, $previous);
    }

    /**
     * Get the desired HTTP status code for this exception.
     *
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * Get the desired HTTP headers for this exception.
     *
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array<mixed> $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * Send the response for this type of exception.
     *
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        $message = $this->getMessage();

        // Front-end JSON handling. API-side handling managed via handler.
        if ($request->wantsJson()) {
            return response()->json(['error' => $message], $this->getStatusCode());
        }

        if (!empty($message)) {
            session()->flash('error', $message);
        }

        return redirect($this->redirectLocation);
    }
}
