<?php

namespace BookStack\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        NotFoundException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($this->isApiRequest($request)) {
            return $this->renderApiException($e);
        }

        return parent::render($request, $e);
    }

    /**
     * Check if the given request is an API request.
     */
    protected function isApiRequest(Request $request): bool
    {
        return strpos($request->path(), 'api/') === 0;
    }

    /**
     * Render an exception when the API is in use.
     */
    protected function renderApiException(Exception $e): JsonResponse
    {
        $code = $e->getCode() === 0 ? 500 : $e->getCode();
        $headers = [];
        if ($e instanceof HttpException) {
            $code = $e->getStatusCode();
            $headers = $e->getHeaders();
        }

        $responseData = [
            'error' => [
                'message' => $e->getMessage(),
            ]
        ];

        if ($e instanceof ValidationException) {
            $responseData['error']['validation'] = $e->errors();
            $code = $e->status;
        }

        $responseData['error']['code'] = $code;
        return new JsonResponse($responseData, $code, $headers);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json($exception->errors(), $exception->status);
    }
}
