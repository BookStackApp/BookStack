<?php

namespace BookStack\Http\Middleware;

use BookStack\Translation\LocaleManager;
use Closure;

class Localization
{
    public function __construct(
        protected LocaleManager $localeManager
    ) {
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
        // Share details of the user's locale for use in views
        $userLocale = $this->localeManager->getForUser(user());
        view()->share('locale', $userLocale);

        // Set locale for system components
        app()->setLocale($userLocale->appLocale());

        return $next($request);
    }
}
