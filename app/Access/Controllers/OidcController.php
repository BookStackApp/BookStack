<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\Oidc\OidcException;
use BookStack\Access\Oidc\OidcService;
use BookStack\Http\Controller;
use Illuminate\Http\Request;

class OidcController extends Controller
{
    protected OidcService $oidcService;

    public function __construct(OidcService $oidcService)
    {
        $this->oidcService = $oidcService;
        $this->middleware('guard:oidc');
    }

    /**
     * Start the authorization login flow via OIDC.
     */
    public function login()
    {
        try {
            $loginDetails = $this->oidcService->login();
        } catch (OidcException $exception) {
            $this->showErrorNotification($exception->getMessage());

            return redirect('/login');
        }

        session()->flash('oidc_state', $loginDetails['state']);

        return redirect($loginDetails['url']);
    }

    /**
     * Authorization flow redirect callback.
     * Processes authorization response from the OIDC Authorization Server.
     */
    public function callback(Request $request)
    {
        $storedState = session()->pull('oidc_state');
        $responseState = $request->query('state');

        if ($storedState !== $responseState) {
            $this->showErrorNotification(trans('errors.oidc_fail_authed', ['system' => config('oidc.name')]));

            return redirect('/login');
        }

        try {
            $this->oidcService->processAuthorizeResponse($request->query('code'));
        } catch (OidcException $oidcException) {
            $this->showErrorNotification($oidcException->getMessage());

            return redirect('/login');
        }

        return redirect()->intended();
    }

    /**
     * Log the user out then start the OIDC RP-initiated logout process.
     */
    public function logout()
    {
        return redirect($this->oidcService->logout());
    }
}
