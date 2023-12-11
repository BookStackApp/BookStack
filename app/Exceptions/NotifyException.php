<?php

namespace BookStack\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class NotifyException extends Exception implements Responsable, HttpExceptionInterface
{
    public $message;
    public string $redirectLocation;
    protected int $status;

    public function __construct(string $message, string $redirectLocation = '/', int $status = 500)
    {
        $this->message = $message;
        $this->redirectLocation = $redirectLocation;
        $this->status = $status;

        parent::__construct();
    }

    /**
     * Get the desired HTTP status code for this exception.
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * Get the desired HTTP headers for this exception.
     */
    public function getHeaders(): array
    {
        return [];
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
