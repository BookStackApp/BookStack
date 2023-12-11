<?php

namespace BookStack\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
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
     * @param \Throwable $exception
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception                $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
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
    public function prepareForOutOfMemory(callable $onOutOfMemory)
    {
        $this->onOutOfMemory = $onOutOfMemory;
    }

    /**
     * Forget the current out of memory handler, if existing.
     */
    public function forgetOutOfMemoryHandler()
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

        $responseData = [
            'error' => [
                'message' => $e->getMessage(),
            ],
        ];

        if ($e instanceof ValidationException) {
            $responseData['error']['message'] = 'The given data was invalid.';
            $responseData['error']['validation'] = $e->errors();
            $code = $e->status;
        }

        $responseData['error']['code'] = $code;

        return new JsonResponse($responseData, $code, $headers);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param \Illuminate\Http\Request                   $request
     * @param \Illuminate\Validation\ValidationException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json($exception->errors(), $exception->status);
    }
}
