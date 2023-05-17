<?php

namespace BookStack\Http\Middleware;

use BookStack\Access\EmailConfirmationService;
use BookStack\Users\Models\User;
use Closure;

/**
 * Check that the user's email address is confirmed.
 *
 * As of v21.08 this is technically not required but kept as a prevention
 * to log out any users that may be logged in but in an "awaiting confirmation" state.
 * We'll keep this for a while until it'd be very unlikely for a user to be upgrading from
 * a pre-v21.08 version.
 *
 * Ideally we'd simply invalidate all existing sessions upon update but that has
 * proven to be a lot more difficult than expected.
 */
class CheckEmailConfirmed
{
    protected $confirmationService;

    public function __construct(EmailConfirmationService $confirmationService)
    {
        $this->confirmationService = $confirmationService;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = auth()->user();
        if (auth()->check() && !$user->email_confirmed && $this->confirmationService->confirmationRequired()) {
            auth()->logout();

            return redirect()->to('/');
        }

        return $next($request);
    }
}
