<?php namespace BookStack\Http\Controllers;

use Illuminate\Http\Request;

class UserApiTokenController extends Controller
{

    /**
     * Show the form to create a new API token.
     */
    public function create(int $userId)
    {
        $this->checkPermission('access-api');

        // TODO - Form
        return 'test';
    }


}
