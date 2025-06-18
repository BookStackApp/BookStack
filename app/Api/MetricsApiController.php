<?php

/**
 * MetricsApiController
 * 
 * Provides a lightweight endpoint for retrieving basic BookStack usage statistics,
 * including the total count of shelves, books, pages, and users. This endpoint
 * is useful for health checks, dashboards, or external monitoring integrations.
 * 
 * Route: GET /api/status
 */

namespace BookStack\Api;

use BookStack\Http\Controller;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;

class MetricsApiController extends Controller
{
    /**
     * Return basic system metrics for shelves, books, pages, and users.
     */
    public function getStatus()
    {
        return response()->json([
            'shelves' => Bookshelf::count(),
            'books'   => Book::count(),
            'pages'   => Page::count(),
            'users'   => User::count(),
        ]);
    }
}

