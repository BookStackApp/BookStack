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
    public function login(string $provider)
    {
        try {
            $loginDetails = $this->oidcService->login($provider);
        } catch (OidcException $exception) {
            $this->showErrorNotification($exception->getMessage());

            return redirect('/login');
        }

        session()->flash("oidc_state_{$provider}", $loginDetails['state']);

        return redirect($loginDetails['url']);
    }

    /**
     * Authorization flow redirect callback.
     * Processes authorization response from the OIDC Authorization Server.
     */
    public function callback(Request $request, string $provider)
    {
        $storedState = session()->pull("oidc_state_{$provider}");
        $responseState = $request->query('state');

        if ($storedState !== $responseState) {
            $this->showErrorNotification(trans('errors.oidc_fail_authed', ['system' => config("oidc.providers.{$provider}.name", 'OIDC')]));

            return redirect('/login');
        }

        try {
            $this->oidcService->processAuthorizeResponse($request->query('code'), $provider);
        } catch (OidcException $oidcException) {
            $this->showErrorNotification($oidcException->getMessage());

            return redirect('/login');
        }
        
        session(['oidc_provider' => $provider]);
        return redirect()->intended();
    }

    /**
     * Log the user out then start the OIDC RP-initiated logout process.
     */
    public function logout(string $provider)
    {
        return redirect($this->oidcService->logout($provider));
    }
}
