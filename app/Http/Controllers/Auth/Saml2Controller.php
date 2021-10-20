<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\Saml2Service;
use BookStack\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Str;

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
            'Content-Type' => 'text/xml',
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
     * Assertion Consumer Service start URL. Takes the SAMLResponse from the IDP.
     * Due to being an external POST request, we likely won't have context of the
     * current user session due to lax cookies. To work around this we store the
     * SAMLResponse data and redirect to the processAcs endpoint for the actual
     * processing of the request with proper context of the user session.
     */
    public function startAcs(Request $request)
    {
        // Note: This is a bit of a hack to prevent a session being stored
        // on the response of this request. Within Laravel7+ this could instead
        // be done via removing the StartSession middleware from the route.
        config()->set('session.driver', 'array');

        $samlResponse = $request->get('SAMLResponse', null);

        if (empty($samlResponse)) {
            $this->showErrorNotification(trans('errors.saml_fail_authed', ['system' => config('saml2.name')]));
            return redirect('/login');
        }

        $acsId = Str::random(16);
        $cacheKey = 'saml2_acs:' . $acsId;
        cache()->set($cacheKey, encrypt($samlResponse), 10);

        return redirect()->guest('/saml2/acs?id=' . $acsId);
    }

    /**
     * Assertion Consumer Service process endpoint.
     * Processes the SAML response from the IDP with context of the current session.
     * Takes the SAML request from the cache, added by the startAcs method above.
     */
    public function processAcs(Request $request)
    {
        $acsId = $request->get('id', null);
        $cacheKey = 'saml2_acs:' . $acsId;
        $samlResponse = null;
        try {
            $samlResponse = decrypt(cache()->pull($cacheKey));
        } catch (\Exception $exception) {}
        $requestId = session()->pull('saml2_request_id', 'unset');

        if (empty($acsId) || empty($samlResponse)) {
            $this->showErrorNotification(trans('errors.saml_fail_authed', ['system' => config('saml2.name')]));
            return redirect('/login');
        }

        $user = $this->samlService->processAcsResponse($requestId, $samlResponse);
        if (is_null($user)) {
            $this->showErrorNotification(trans('errors.saml_fail_authed', ['system' => config('saml2.name')]));
            return redirect('/login');
        }

        return redirect()->intended();
    }
}
