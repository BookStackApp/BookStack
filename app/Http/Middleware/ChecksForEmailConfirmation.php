<?php

namespace BookStack\Http\Middleware;

use Illuminate\Http\Request;

trait ChecksForEmailConfirmation
{

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

    /**
     * Provide an error response for when the current user's email is not confirmed
     * in a system which requires it.
     */
    protected function emailConfirmationErrorResponse(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'error' => [
                    'code' => 401,
                    'message' => trans('errors.email_confirmation_awaiting')
                ]
            ], 401);
        }

        return redirect('/register/confirm/awaiting');
    }
}