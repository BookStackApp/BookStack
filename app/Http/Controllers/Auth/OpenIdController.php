<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\OpenIdService;
use BookStack\Http\Controllers\Controller;

class OpenIdController extends Controller
{

    protected $openidService;

    /**
     * OpenIdController constructor.
     */
    public function __construct(OpenIdService $openidService)
    {
        parent::__construct();
        $this->openidService = $openidService;
        $this->middleware('guard:openid');
    }

    /**
     * Start the authorization login flow via OpenId Connect.
     */
    public function login()
    {
        $loginDetails = $this->openidService->login();
        session()->flash('openid_state', $loginDetails['state']);

        return redirect($loginDetails['url']);
    }

    /**
     * Start the logout flow via OpenId Connect.
     */
    public function logout()
    {
        $logoutDetails = $this->openidService->logout();

        if ($logoutDetails['id']) {
            session()->flash('saml2_logout_request_id', $logoutDetails['id']);
        }

        return redirect($logoutDetails['url']);
    }

    /**
     * Authorization flow Redirect.
     * Processes authorization response from the OpenId Connect Authorization Server.
     */
    public function redirect()
    {
        $storedState = session()->pull('openid_state');
        $responseState = request()->query('state');

        if ($storedState !== $responseState) {
            $this->showErrorNotification(trans('errors.openid_fail_authed', ['system' => config('saml2.name')]));
            return redirect('/login');
        }

        $user = $this->openidService->processAuthorizeResponse(request()->query('code'));
        if ($user === null) {
            $this->showErrorNotification(trans('errors.openid_fail_authed', ['system' => config('saml2.name')]));
            return redirect('/login');
        }

        return redirect()->intended();
    }
}
