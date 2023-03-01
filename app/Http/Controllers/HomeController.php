<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityQueries;
use BookStack\Entities\BasicListItem;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Page;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index(Request $request, ActivityQueries $activities)
    {
        $activeUsers = $activities->recentlyActiveUsers(3);

        $newSymbols = Page::getVisiblePagesInBookshelf('symbols')
            ->orderBy('created_at', 'desc')
            ->take(10)
            // ->select(Page::$listAttributes)
            ->get();

        $latestDrafts = Page::getVisiblePagesInBookshelf('contribute')
            ->orderBy('created_at', 'desc')
            ->take(3)
            // ->select(Page::$listAttributes)
            ->get();

        $latestCommunityReviews = Page::getVisiblePagesInBookshelf('contribute')
            // ->where('book_id', '=', '5')
            ->where('book_id', '=',  Book::getBySlug('community-review', true)->id)
            ->orderBy('created_at', 'desc')
            // ->take(3)
            // ->select(Page::$listAttributes)
            ->get();

        $quickLinks = collect([
            new BasicListItem('/shelves/symbols', 'All Symbols', 'See all of the official symbols', 'star-circle'),
            // new BasicListItem('http://localhost:4000', 'Community Tasks', 'Go to the task manager', 'check'),
            new BasicListItem(env('TASK_MANAGER_URL', null), 'Tasks', 'Go to the community task manager', 'check'),
            // new BasicListItem('/shelves/to-do-lists/all', 'To-Do Items', 'Help out by checking off to-do items!', 'check'),
            new BasicListItem('/shelves/getting-started/all', 'How Can I Help?', 'Learn how you can help Symbolpedia!', 'info'),
            // ...(Bookshelf::getBySlug('contribute')->visibleBooks()->get()->all() ?? [], true),
            ...Bookshelf::getBySlug('contribute')->visibleBooks()->get()->all(),
            // Book::getBySlug('blog-post-symbols-to-extract', true),
            // Book::getBySlug('blog-post-to-dos', true),
        ]);

        // $test = Book::getBySlug('drafts', true);

        // $recentUpdates = Page::getAllVisiblePages()
        // ->orderBy('created_at', 'desc')
        // ->take(2)
        // ->get();
        $recentUpdates = Page::getVisiblePagesInBookshelf('symbols')
        ->orderBy('updated_at', 'desc')
        ->where('revision_count', '>', 1)
        ->take(3)
        ->select(Page::$listAttributes)
        ->get();

        $symbolTypesList = Bookshelf::getBySlug('symbols', true)->visibleBooks()->get();

        $homepageOptions = ['default', 'books', 'bookshelves', 'page'];
        $homepageOption = setting('app-homepage-type', 'default');
        if (!in_array($homepageOption, $homepageOptions)) {
            $homepageOption = 'default';
        }

        $commonData = [
            'activeUsers' => $activeUsers,
            'latestDrafts' => $latestDrafts,
            'latestCommunityReviews' => $latestCommunityReviews,
            'newSymbols' => $newSymbols,
            'quickLinks' => $quickLinks,
            'symbolTypesList' => $symbolTypesList,
            'recentUpdates' => $recentUpdates,
        ];

        return view('home.default', $commonData);
    }

    /**
     * Show the view for /robots.txt.
     */
    public function robots()
    {
        $sitePublic = setting('app-public', false);
        $allowRobots = config('app.allow_robots');

        if ($allowRobots === null) {
            $allowRobots = $sitePublic;
        }

        return response()
            ->view('misc.robots', ['allowRobots' => $allowRobots])
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Show the route for 404 responses.
     */
    public function notFound()
    {
        return response()->view('errors.404', [], 404);
    }
}
