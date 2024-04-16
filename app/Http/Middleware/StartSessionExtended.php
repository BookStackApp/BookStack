<?php

namespace BookStack\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession as Middleware;

/**
 * An extended version of the default Laravel "StartSession" middleware
 * with customizations applied as required:
 *
 * - Adds filtering for the request URLs stored in session history.
 */
class StartSessionExtended extends Middleware
{
    protected static array $pathPrefixesExcludedFromHistory = [
        'uploads/images/'
    ];

    /**
     * @inheritdoc
     */
    protected function storeCurrentUrl(Request $request, $session): void
    {
        $requestPath = strtolower($request->path());
        foreach (static::$pathPrefixesExcludedFromHistory as $excludedPath) {
            if (str_starts_with($requestPath, $excludedPath)) {
                return;
            }
        }

        parent::storeCurrentUrl($request, $session);
    }
}
