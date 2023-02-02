<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityQueries;
use BookStack\Entities\BasicListItem;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\RecentlyViewed;
use BookStack\Entities\Queries\TopFavourites;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Entities\Tools\PageContent;
use BookStack\Util\SimpleListOptions;
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
            ->select(Page::$listAttributes)
            ->get();

        $latestDrafts = Page::getVisiblePagesInBookshelf('contribute')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->select(Page::$listAttributes)
            ->get();

        // $blogTodos = Book::getBySlug('blog-post-symbols-to-extract')->visible();

        $quickLinks = collect([
            new BasicListItem('/shelves/symbols/all', 'All Symbols', 'See all of the official symbols', 'star-circle'),
            // new BasicListItem('/shelves/to-do-lists/all', 'To-Do Items', 'Help out by checking off to-do items!', 'check'),
            new BasicListItem('/shelves/to-do-lists/all', 'How Can I Help?', 'Learn how you can help Symbolpedia!', 'info'),
            ...Bookshelf::getBySlug('contribute')->visibleBooks()->get()->all(),
            // Book::getBySlug('blog-post-symbols-to-extract'),
            // Book::getBySlug('blog-post-to-dos'),
        ]);

        // $test = Book::getBySlug('drafts');

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

        $symbolTypesList = Bookshelf::getBySlug('symbols')->visibleBooks()->get();

        $homepageOptions = ['default', 'books', 'bookshelves', 'page'];
        $homepageOption = setting('app-homepage-type', 'default');
        if (!in_array($homepageOption, $homepageOptions)) {
            $homepageOption = 'default';
        }

        $commonData = [
            'activeUsers' => $activeUsers,
            'latestDrafts' => $latestDrafts,
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
