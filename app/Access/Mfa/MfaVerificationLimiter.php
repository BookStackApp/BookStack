<?php

namespace BookStack\Access\Mfa;

use BookStack\Exceptions\NotifyException;
use BookStack\Users\Models\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * A rate limit specifically for MFA verification.
 * Limits across both the attempted user (on a tight limit) and the
 * request IP (on a less strict limit).
 */
class MfaVerificationLimiter
{
    protected int $maxUserAttemptsPerMinute = 5;
    protected int $maxIpAttemptsPerMinute = 60;

    public function __construct(
        protected RateLimiter $rateLimiter
    ) {
    }

    public function throwException(): never
    {
        throw new NotifyException(
            trans('auth.mfa_throttle', ['seconds' => 60]),
            '/login',
            Response::HTTP_TOO_MANY_REQUESTS
        );
    }

    public function incrementAttempts(User $user, Request $request): void
    {
        $this->rateLimiter->hit($this->getUserKey($user));
        $this->rateLimiter->hit($this->getRequestKey($request));
    }

    public function decrementAttempts(User $user, Request $request): void
    {
        $this->rateLimiter->decrement($this->getUserKey($user));
        $this->rateLimiter->decrement($this->getRequestKey($request));
    }

    public function hasHitLimit(User $user, Request $request): bool
    {
        return $this->rateLimiter->tooManyAttempts($this->getUserKey($user), $this->maxUserAttemptsPerMinute + 1)
            || $this->rateLimiter->tooManyAttempts($this->getRequestKey($request), $this->maxIpAttemptsPerMinute + 1);
    }

    protected function getUserKey(User $user): string
    {
        return "mfa-attempt::user::{$user->id}";
    }

    protected function getRequestKey(Request $request): string
    {
        return "mfa-attempt::request::{$request->ip()}";
    }
}
