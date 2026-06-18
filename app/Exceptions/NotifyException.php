<?php

namespace BookStack\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * An exception that is thrown to notify the user of something which went wrong.
 * Typically these should be translated messages since they will be shown to the end user
 * via a pop up notification error message in the UI.
 *
 * This exception is not intended to be used for internal system/application errors,
 * and therefore will not be logged by the exception handler.
 */
class NotifyException extends Exception implements Responsable, HttpExceptionInterface
{
    public function __construct(
        string $message,
        public string $redirectLocation = '/',
        protected int $status = 500
    ) {
        $this->message = $message;
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
