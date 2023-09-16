<?php

namespace BookStack\Access\Controllers;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ThrottlesLogins
{
    /**
     * Determine if the user has too many failed login attempts.
     */
    protected function hasTooManyLoginAttempts(Request $request): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    /**
     * Increment the login attempts for the user.
     */
    protected function incrementLoginAttempts(Request $request): void
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            $this->decayMinutes() * 60
        );
    }

    /**
     * Redirect the user after determining they are locked out.
     * @throws ValidationException
     */
    protected function sendLockoutResponse(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Clear the login locks for the given user credentials.
     */
    protected function clearLoginAttempts(Request $request): void
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input($this->username())) . '|' . $request->ip());
    }

    /**
     * Get the rate limiter instance.
     */
    protected function limiter(): RateLimiter
    {
        return app()->make(RateLimiter::class);
    }

    /**
     * Get the maximum number of attempts to allow.
     */
    public function maxAttempts(): int
    {
        return 5;
    }

    /**
     * Get the number of minutes to throttle for.
     */
    public function decayMinutes(): int
    {
        return 1;
    }
}
