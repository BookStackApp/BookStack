<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\Saml2Service;
use BookStack\Http\Controllers\Controller;

class Saml2Controller extends Controller
{

    protected $samlService;

    /**
     * Saml2Controller constructor.
     */
    public function __construct(Saml2Service $samlService)
    {
        $this->samlService = $samlService;
        $this->middleware('guard:saml2');
    }

    /**
     * Start the login flow via SAML2.
     */
    public function login()
    {
        $loginDetails = $this->samlService->login();
        session()->flash('saml2_request_id', $loginDetails['id']);

        return redirect($loginDetails['url']);
    }

    /**
     * Start the logout flow via SAML2.
     */
    public function logout()
    {
        $logoutDetails = $this->samlService->logout();

        if ($logoutDetails['id']) {
            session()->flash('saml2_logout_request_id', $logoutDetails['id']);
        }

        return redirect($logoutDetails['url']);
    }

    /*
     * Get the metadata for this SAML2 service provider.
     */
    public function metadata()
    {
        $metaData = $this->samlService->metadata();
        return response()->make($metaData, 200, [
            'Content-Type' => 'text/xml'
        ]);
    }

    /**
     * Single logout service.
     * Handle logout requests and responses.
     */
    public function sls()
    {
        $requestId = session()->pull('saml2_logout_request_id', null);
        $redirect = $this->samlService->processSlsResponse($requestId) ?? '/';
        return redirect($redirect);
    }

    /**
     * Assertion Consumer Service.
     * Processes the SAML response from the IDP.
     */
    public function acs()
    {
        $requestId = session()->pull('saml2_request_id', null);

        $user = $this->samlService->processAcsResponse($requestId);
        if ($user === null) {
            $this->showErrorNotification(trans('errors.saml_fail_authed', ['system' => config('saml2.name')]));
            return redirect('/login');
        }

        return redirect()->intended();
    }

}
