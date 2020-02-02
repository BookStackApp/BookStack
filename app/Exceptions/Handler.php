<?php

namespace BookStack\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return mixed
     * @throws Exception
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($this->isApiRequest($request)) {
            return $this->renderApiException($e);
        }

        // Handle notify exceptions which will redirect to the
        // specified location then show a notification message.
        if ($this->isExceptionType($e, NotifyException::class)) {
            $message = $this->getOriginalMessage($e);
            if (!empty($message)) {
                session()->flash('error', $message);
            }
            return redirect($e->redirectLocation);
        }

        // Handle pretty exceptions which will show a friendly application-fitting page
        // Which will include the basic message to point the user roughly to the cause.
        if ($this->isExceptionType($e, PrettyException::class)  && !config('app.debug')) {
            $message = $this->getOriginalMessage($e);
            $code = ($e->getCode() === 0) ? 500 : $e->getCode();
            return response()->view('errors/' . $code, ['message' => $message], $code);
        }

        // Handle 404 errors with a loaded session to enable showing user-specific information
        if ($this->isExceptionType($e, NotFoundHttpException::class)) {
            return \Route::respondWithRoute('fallback');
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
     * Check the exception chain to compare against the original exception type.
     * @param Exception $e
     * @param $type
     * @return bool
     */
    protected function isExceptionType(Exception $e, $type)
    {
        do {
            if (is_a($e, $type)) {
                return true;
            }
        } while ($e = $e->getPrevious());
        return false;
    }

    /**
     * Get original exception message.
     * @param Exception $e
     * @return string
     */
    protected function getOriginalMessage(Exception $e)
    {
        do {
            $message = $e->getMessage();
        } while ($e = $e->getPrevious());
        return $message;
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
