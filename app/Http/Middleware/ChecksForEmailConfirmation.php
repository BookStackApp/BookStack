<?php

namespace BookStack\Http\Middleware;

use BookStack\Exceptions\UnauthorizedException;
use Illuminate\Http\Request;

trait ChecksForEmailConfirmation
{
    /**
     * Check if the current user has a confirmed email if the instance deems it as required.
     * Throws if confirmation is required by the user.
     * @throws UnauthorizedException
     */
    protected function ensureEmailConfirmedIfRequested()
    {
        if ($this->awaitingEmailConfirmation()) {
            throw new UnauthorizedException(trans('errors.email_confirmation_awaiting'));
        }
    }

    /**
     * Check if email confirmation is required and the current user is awaiting confirmation.
     */
    protected function awaitingEmailConfirmation(): bool
    {
        if (auth()->check()) {
            $requireConfirmation = (setting('registration-confirmation') || setting('registration-restrict'));
            if ($requireConfirmation && !auth()->user()->email_confirmed) {
                return true;
            }
        }

        return false;
    }
}