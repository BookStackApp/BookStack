<?php

namespace BookStack\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use BookStack\Entities\Models\PageRevision;

class RedirectOldPageSlugs
{
    public function handle(Request $request, Closure $next)
    {
        // Skip system/API routes
        if ($request->is('api/*', 'assets/*', 'dist/*', 'uploads/*', '_debugbar/*', 'link/*', 'saml2/*')) {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        if (empty($path)) {
            return $next($request);
        }

        // Extract last path segment (potential page slug)
        $segments = explode('/', $path);
        $lastSegment = end($segments);

        // Look for this slug in page revisions (only if it's not the current slug)
        $revision = PageRevision::where('slug', $lastSegment)
            ->whereHas('page', function ($query) use ($lastSegment) {
                $query->where('slug', '!=', $lastSegment);
            })
            ->first();

        if ($revision) {
            // Redirect to stable permalink: /link/{page_id}
            return redirect('/link/' . $revision->page_id, 301);
        }

        return $next($request);
    }
}
