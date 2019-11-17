<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\Saml2Service;
use BookStack\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Saml2Controller extends Controller
{

    protected $samlService;

    /**
     * Saml2Controller constructor.
     */
    public function __construct(Saml2Service $samlService)
    {
        parent::__construct();
        $this->samlService = $samlService;
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
        // TODO
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
