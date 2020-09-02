<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use BookStack\Auth\User;

class SsoLogin
{
    /**
     * Check if SSO variable is set and auto login if they are.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // auto login if SSO env variable is set (ie: APP_SSO_KEY=HTTP_MAIL which will look at $_SERVER['HTTP_MAIL'])
        //  note: remember to "artisan config:clear" when updating the .env file

        // from .env
        // APP_SSO_KEY=HTTP_MAIL   <-- this is the $_SERVER variable to check
        // APP_SSO_DISCOVER=email  <-- this is the field to check exists in Users table

        $sso_key = env("APP_SSO_KEY", false);
        $sso_discover = env("APP_SSO_DISCOVER", false);

        if ($sso_key && $sso_discover) {
            if (Auth::check()) {
                // they're already logged in so no need to check for SSO match.
            } else {
                // if they're not already logged in try and look up an existing user
                $uid = @User::where($sso_discover, $_SERVER[$sso_key])->first()->id;
                if ($uid) {
                    Auth::loginUsingId($uid, true);
                }
            }
        }

        return $next($request);
    }
}
