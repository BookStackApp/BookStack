<?php

namespace BookStack\Http\Controllers;

use HttpRequestException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
        // Get a user instance for the current user
        $user = auth()->user();
        if (!$user) $user = User::getDefault();

        // Share variables with views
        view()->share('signedIn', auth()->check());
        view()->share('currentUser', $user);

        // Share variables with controllers
        $this->currentUser = $user;
        $this->signedIn = auth()->check();
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
     * On a permission error redirect to home and display
     * the error as a notification.
     */
    protected function showPermissionError()
    {
        Session::flash('error', trans('errors.permission'));
        throw new HttpResponseException(
            redirect('/')
        );
    }

    /**
     * Checks for a permission.
     *
     * @param $permissionName
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    protected function checkPermission($permissionName)
    {
        if (!$this->currentUser || !$this->currentUser->can($permissionName)) {
            dd($this->currentUser);
            $this->showPermissionError();
        }

        return true;
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

}
