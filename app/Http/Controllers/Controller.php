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
     * Adds the page title into the view.
     * @param $title
     */
    public function setPageTitle($title)
    {
        view()->share('pageTitle', $title);
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
            Session::flash('error', trans('errors.permission'));
            throw new HttpResponseException(
                redirect('/')
            );
        }

        return true;
    }

    protected function checkPermissionOr($permissionName, $callback)
    {
        $callbackResult = $callback();
        if ($callbackResult === false) $this->checkPermission($permissionName);
        return true;
    }

}
