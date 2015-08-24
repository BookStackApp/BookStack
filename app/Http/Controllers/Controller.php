<?php

namespace Oxbow\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Oxbow\User;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        view()->share('signedIn', Auth::check());
        $user = Auth::user();
        if(!$user) {
            $user = User::getDefault();
        }
        view()->share('user', $user);
    }

}
