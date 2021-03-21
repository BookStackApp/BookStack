<?php

namespace BookStack\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests as Middleware;

class ThrottleApiRequests extends Middleware
{

    /**
     * Resolve the number of attempts if the user is authenticated or not.
     */
    protected function resolveMaxAttempts($request, $maxAttempts)
    {
        return (int) config('api.requests_per_minute');
    }
}
