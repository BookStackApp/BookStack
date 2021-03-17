<?php

namespace BookStack\Http\Middleware;

use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use Closure;

class RunThemeActions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $earlyResponse = Theme::dispatch(ThemeEvents::WEB_MIDDLEWARE_BEFORE, $request);
        if (!is_null($earlyResponse)) {
            return $earlyResponse;
        }

        $response = $next($request);
        $response = Theme::dispatch(ThemeEvents::WEB_MIDDLEWARE_AFTER, $request, $response) ?? $response;
        return $response;
    }
}
