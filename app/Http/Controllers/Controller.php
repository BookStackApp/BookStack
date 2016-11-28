<?php

namespace BookStack\Http\Controllers;

use BookStack\Ownable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use BookStack\User;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * @var User static
     */
    protected $currentUser;
    /**
     * @var bool
     */
    protected $signedIn;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            // Get a user instance for the current user
            $user = user();

            // Share variables with controllers
            $this->currentUser = $user;
            $this->signedIn = auth()->check();

            // Share variables with views
            view()->share('signedIn', $this->signedIn);
            view()->share('currentUser', $user);

            return $next($request);
        });
    }

    /**
     * Stops the application and shows a permission error if
     * the application is in demo mode.
     */
    protected function preventAccessForDemoUsers()
    {
        if (config('app.env') === 'demo') $this->showPermissionError();
    }

    /**
     * Adds the page title into the view.
     * @param $title
     */
    public function setPageTitle($title)
    {
        view()->share('pageTitle', $title);
    }

    /**
     * On a permission error redirect to home and display.
     * the error as a notification.
     */
    protected function showPermissionError()
    {
        if (request()->wantsJson()) {
            $response = response()->json(['error' => trans('errors.permissionJson')], 403);
        } else {
            $response = redirect('/');
            session()->flash('error', trans('errors.permission'));
        }

        throw new HttpResponseException($response);
    }

    /**
     * Checks for a permission.
     * @param string $permissionName
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    protected function checkPermission($permissionName)
    {
        if (!user() || !user()->can($permissionName)) {
            $this->showPermissionError();
        }
        return true;
    }

    /**
     * Check the current user's permissions against an ownable item.
     * @param $permission
     * @param Ownable $ownable
     * @return bool
     */
    protected function checkOwnablePermission($permission, Ownable $ownable)
    {
        if (userCan($permission, $ownable)) return true;
        return $this->showPermissionError();
    }

    /**
     * Check if a user has a permission or bypass if the callback is true.
     * @param $permissionName
     * @param $callback
     * @return bool
     */
    protected function checkPermissionOr($permissionName, $callback)
    {
        $callbackResult = $callback();
        if ($callbackResult === false) $this->checkPermission($permissionName);
        return true;
    }

    /**
     * Send back a json error message.
     * @param string $messageText
     * @param int $statusCode
     * @return mixed
     */
    protected function jsonError($messageText = "", $statusCode = 500)
    {
        return response()->json(['message' => $messageText], $statusCode);
    }

    /**
     * Create the response for when a request fails validation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if ($request->expectsJson()) {
            return response()->json(['validation' => $errors], 422);
        }

        return redirect()->to($this->getRedirectUrl())
            ->withInput($request->input())
            ->withErrors($errors, $this->errorBag());
    }

}
