<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MfaController extends Controller
{
    /**
     * Show the view to setup MFA for the current user.
     */
    public function setup()
    {
        // TODO - Redirect back to profile/edit if already setup?
        // Show MFA setup route
        return view('mfa.setup');
    }

    public function generateQr()
    {
        // https://github.com/antonioribeiro/google2fa#how-to-generate-and-use-two-factor-authentication

        // Generate secret key
        // Store key in session?
        // Get user to verify setup via responding once.
        // If correct response, Save key against user
    }
}
