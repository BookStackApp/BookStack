<?php

namespace BookStack\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        NotFoundException::class,
        StoppedAuthenticationException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * A function to run upon out of memory.
     * If it returns a response, that will be provided back to the request
     * upon an out of memory event.
     *
     * @var ?callable(): ?Response
     */
    protected $onOutOfMemory = null;

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     *
     * @return void
     *@throws Throwable
     *
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     */
    public function render($request, Throwable $e): SymfonyResponse
    {
        if ($e instanceof FatalError && str_contains($e->getMessage(), 'bytes exhausted (tried to allocate') && $this->onOutOfMemory) {
            $response = call_user_func($this->onOutOfMemory);
            if ($response) {
                return $response;
            }
        }

        if ($e instanceof PostTooLargeException) {
            $e = new NotifyException(trans('errors.server_post_limit'), '/', 413);
        }

        if ($this->isApiRequest($request)) {
            return $this->renderApiException($e);
        }

        return parent::render($request, $e);
    }

    /**
     * Provide a function to be called when an out of memory event occurs.
     * If the callable returns a response, this response will be returned
     * to the request upon error.
     */
    public function prepareForOutOfMemory(callable $onOutOfMemory): void
    {
        $this->onOutOfMemory = $onOutOfMemory;
    }

    /**
     * Forget the current out of memory handler, if existing.
     */
    public function forgetOutOfMemoryHandler(): void
    {
        $this->onOutOfMemory = null;
    }

    /**
     * Check if the given request is an API request.
     */
    protected function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->path(), 'api/');
    }

    /**
     * Render an exception when the API is in use.
     */
    protected function renderApiException(Throwable $e): JsonResponse
    {
        $code = 500;
        $headers = [];

        if ($e instanceof HttpExceptionInterface) {
            $code = $e->getStatusCode();
            $headers = $e->getHeaders();
        }

        if ($e instanceof ModelNotFoundException) {
            $code = 404;
        }

        $errorCode = $this->getErrorCodeFromException($e);
        $responseData = [
            'error' => [
                'message' => $e->getMessage() ?: $this->getDefaultErrorMessage($code),
                'code'   => $errorCode,
                'status' => $code,
            ],
        ];

        if ($e instanceof ValidationException) {
            $responseData['error']['message'] = 'The given data was invalid.';
            $responseData['error']['validation'] = $e->errors();
            $responseData['error']['code'] = 'VALIDATION_ERROR';
            $code = $e->status;
        }

        return new JsonResponse($responseData, $code, $headers);
    }

    /**
     * Get a machine-readable error code from an exception.
     */
    protected function getErrorCodeFromException(Throwable $e): string
    {
        if ($e instanceof HttpExceptionInterface) {
            return match ($e->getStatusCode()) {
                400 => 'BAD_REQUEST',
                401 => 'UNAUTHORIZED',
                403 => 'FORBIDDEN',
                404 => 'NOT_FOUND',
                405 => 'METHOD_NOT_ALLOWED',
                409 => 'CONFLICT',
                422 => 'UNPROCESSABLE_ENTITY',
                429 => 'TOO_MANY_REQUESTS',
                500 => 'INTERNAL_ERROR',
                502 => 'BAD_GATEWAY',
                503 => 'SERVICE_UNAVAILABLE',
                default => 'HTTP_ERROR',
            };
        }

        if ($e instanceof ModelNotFoundException) {
            return 'NOT_FOUND';
        }

        if ($e instanceof ValidationException) {
            return 'VALIDATION_ERROR';
        }

        return 'INTERNAL_ERROR';
    }

    /**
     * Get a default error message for a given HTTP status code.
     */
    protected function getDefaultErrorMessage(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Bad request',
            401 => 'Unauthenticated',
            403 => 'Forbidden',
            404 => 'Resource not found',
            405 => 'Method not allowed',
            409 => 'Conflict',
            422 => 'Validation failed',
            429 => 'Too many requests',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
            default => 'An error occurred',
        };
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param Request $request
     */
    protected function unauthenticated($request, AuthenticationException $exception): SymfonyResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => [
                    'message' => 'Unauthenticated',
                    'code'    => 'UNAUTHORIZED',
                    'status'  => 401,
                ],
            ], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param Request $request
     */
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return new JsonResponse([
            'error' => [
                'message' => 'The given data was invalid.',
                'code' => 'VALIDATION_ERROR',
                'status' => $exception->status,
                'validation' => $exception->errors(),
            ],
        ], $exception->status);
    }
}
