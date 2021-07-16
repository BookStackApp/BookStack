<?php

namespace BookStack\Http\Middleware;

use BookStack\Auth\Access\Mfa\MfaSession;
use Closure;

class EnforceMfaRequirements
{
    protected $mfaSession;

    /**
     * EnforceMfaRequirements constructor.
     */
    public function __construct(MfaSession $mfaSession)
    {
        $this->mfaSession = $mfaSession;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (
            !$this->mfaSession->isVerified()
            && !$request->is('mfa/verify*', 'uploads/images/user/*')
            && $this->mfaSession->requiredForCurrentUser()
        ) {
            return redirect('/mfa/verify');
        }

        // TODO - URI wildcard exceptions above allow access to the 404 page of this user
        //  which could then expose content. Either need to lock that down (Tricky to do image thing)
        //  or prevent any level of auth until verified.

        // TODO - Need to redirect to setup if not configured AND ONLY IF NO OPTIONS CONFIGURED
        //    Might need to change up such routes to start with /configure/ for such identification.
        //    (Can't allow access to those if already configured)
        // TODO - Store mfa_pass into session for future requests?

        return $next($request);
    }
}
