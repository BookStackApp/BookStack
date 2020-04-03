<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\CasService;
use BookStack\Auth\Access\RegistrationService;
use BookStack\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CasController extends Controller
{

    private $casService;

    /**
     * CasController constructor.
     */
    public function __construct(CasService $casService, RegistrationService $registrationService)
    {
        $this->casService = $casService;
    }

    public function login()
    {
        if (cas()->isAuthenticated()) {
            $this->casService->handleLoginCallback(cas()->user(), cas()->getAttributes());
            return redirect()->intended();
        } else {
            cas()->authenticate();
        }
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        cas()->logout();
    }

}
