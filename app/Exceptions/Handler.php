<?php

namespace BookStack\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpSpec\Exception\Example\ErrorException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;

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
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
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
        // Handle notify exceptions which will redirect to the
        // specified location then show a notification message.
        if ($e instanceof NotifyException) {
            \Session::flash('error', $e->message);
            return response()->redirectTo($e->redirectLocation);
        }

        // Handle pretty exceptions which will show a friendly application-fitting page
        // Which will include the basic message to point the user roughly to the cause.
        if (($e instanceof PrettyException || $e->getPrevious() instanceof PrettyException)  && !config('app.debug')) {
            $message = ($e instanceof PrettyException) ? $e->getMessage() : $e->getPrevious()->getMessage();
            return response()->view('errors/500', ['message' => $message], 500);
        }

        return parent::render($request, $e);
    }
}
