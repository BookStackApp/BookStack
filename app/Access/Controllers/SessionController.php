<?php

declare(strict_types=1);

namespace BookStack\Access\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SessionController
{
    /**
     * View to help refresh a session by providing updating cookies and CSRF tokens
     * for use to other views passed via the PostMessage API.
     */
    public function refresh(): View|RedirectResponse
    {
        if (user()->isGuest()) {
            return redirect(url('/login?session_refresh=true'));
        }

        return view('auth.session-refresh');
    }
}
