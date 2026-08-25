<?php

namespace BookStack\Http\Middleware;

use BookStack\View\ViewBlockManager;
use Closure;
use Illuminate\Http\Request;

/**
 * Custom middleware to clear any local caches in the app which are created on a
 * per-request basis. While this can be somewhat redundant in the normal PHP request
 * lifecycle (since in memory caches are cleared on each request), this can be useful
 * in testing and to prepare for future long-serving PHP runtimes.
 */
class ClearPerRequestCaches
{
    public function __construct(
        protected ViewBlockManager $viewBlockManager,
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $this->clearCaches();

        return $next($request);
    }

    protected function clearCaches(): void
    {
        $this->viewBlockManager->clearLocalCache();
    }
}
