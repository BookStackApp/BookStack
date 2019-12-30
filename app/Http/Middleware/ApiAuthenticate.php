<?php

namespace BookStack\Http\Middleware;

use BookStack\Api\ApiToken;
use BookStack\Http\Request;
use Closure;
use Hash;

class ApiAuthenticate
{

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // TODO - Look to extract a lot of the logic here into a 'Guard'
        // Ideally would like to be able to request API via browser without having to boot
        // the session middleware (in Kernel).

//        $sessionCookieName = config('session.cookie');
//        if ($request->cookies->has($sessionCookieName)) {
//            $sessionCookie = $request->cookies->get($sessionCookieName);
//            $sessionCookie = decrypt($sessionCookie, false);
//            dd($sessionCookie);
//        }

        // Return if the user is already found to be signed in via session-based auth.
        // This is to make it easy to browser the API via browser after just logging into the system.
        if (signedInUser()) {
            return $next($request);
        }

        $authToken = trim($request->header('Authorization', ''));
        if (empty($authToken)) {
            return $this->unauthorisedResponse(trans('errors.api_no_authorization_found'));
        }

        if (strpos($authToken, ':') === false || strpos($authToken, 'Token ') !== 0) {
            return $this->unauthorisedResponse(trans('errors.api_bad_authorization_format'));
        }

        [$id, $secret] = explode(':', str_replace('Token ', '', $authToken));
        $token = ApiToken::query()
            ->where('token_id', '=', $id)
            ->with(['user'])->first();

        if ($token === null) {
            return $this->unauthorisedResponse(trans('errors.api_user_token_not_found'));
        }

        if (!Hash::check($secret, $token->secret)) {
            return $this->unauthorisedResponse(trans('errors.api_incorrect_token_secret'));
        }

        if (!$token->user->can('access-api')) {
            return $this->unauthorisedResponse(trans('errors.api_user_no_api_permission'), 403);
        }

        auth()->login($token->user);

        return $next($request);
    }

    /**
     * Provide a standard API unauthorised response.
     */
    protected function unauthorisedResponse(string $message, int $code = 401)
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
            ]
        ], 401);
    }
}
