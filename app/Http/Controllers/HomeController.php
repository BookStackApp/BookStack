<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityQueries;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\RecentlyViewed;
use BookStack\Entities\Queries\TopFavourites;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\BookshelfRepo;
use BookStack\Entities\Tools\PageContent;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index(ActivityQueries $activities)
    {
        $activity = $activities->latest(10);
        $draftPages = [];

        if ($this->isSignedIn()) {
            $draftPages = Page::visible()
                ->where('draft', '=', true)
                ->where('created_by', '=', user()->id)
                ->orderBy('updated_at', 'desc')
                ->with('book')
                ->take(6)
                ->get();
        }

        $recentFactor = count($draftPages) > 0 ? 0.5 : 1;
        $recents = $this->isSignedIn() ?
            (new RecentlyViewed())->run(12 * $recentFactor, 1)
            : Book::visible()->orderBy('created_at', 'desc')->take(12 * $recentFactor)->get();
        $favourites = (new TopFavourites())->run(6);
        $recentlyUpdatedPages = Page::visible()->with('book')
            ->where('draft', false)
            ->orderBy('updated_at', 'desc')
            ->take($favourites->count() > 0 ? 5 : 10)
            ->select(Page::$listAttributes)
            ->get();

        $homepageOptions = ['default', 'books', 'bookshelves', 'page'];
        $homepageOption = setting('app-homepage-type', 'default');
        if (!in_array($homepageOption, $homepageOptions)) {
            $homepageOption = 'default';
        }

        $commonData = [
            'activity'             => $activity,
            'recents'              => $recents,
            'recentlyUpdatedPages' => $recentlyUpdatedPages,
            'draftPages'           => $draftPages,
            'favourites'           => $favourites,
        ];

        // Add required list ordering & sorting for books & shelves views.
        if ($homepageOption === 'bookshelves' || $homepageOption === 'books') {
            $key = $homepageOption;
            $view = setting()->getForCurrentUser($key . '_view_type');
            $sort = setting()->getForCurrentUser($key . '_sort', 'name');
            $order = setting()->getForCurrentUser($key . '_sort_order', 'asc');

            $sortOptions = [
                'name'       => trans('common.sort_name'),
                'created_at' => trans('common.sort_created_at'),
                'updated_at' => trans('common.sort_updated_at'),
            ];

            $commonData = array_merge($commonData, [
                'view'        => $view,
                'sort'        => $sort,
                'order'       => $order,
                'sortOptions' => $sortOptions,
            ]);
        }

        if ($homepageOption === 'bookshelves') {
            $shelves = app(BookshelfRepo::class)->getAllPaginated(18, $commonData['sort'], $commonData['order']);
            $data = array_merge($commonData, ['shelves' => $shelves]);

            return view('home.shelves', $data);
        }

        if ($homepageOption === 'books') {
            $bookRepo = app(BookRepo::class);
            $books = $bookRepo->getAllPaginated(18, $commonData['sort'], $commonData['order']);
            $data = array_merge($commonData, ['books' => $books]);

            return view('home.books', $data);
        }

        if ($homepageOption === 'page') {
            $homepageSetting = setting('app-homepage', '0:');
            $id = intval(explode(':', $homepageSetting)[0]);
            /** @var Page $customHomepage */
            $customHomepage = Page::query()->where('draft', '=', false)->findOrFail($id);
            $pageContent = new PageContent($customHomepage);
            $customHomepage->html = $pageContent->render(false);

            return view('home.specific-page', array_merge($commonData, ['customHomepage' => $customHomepage]));
        }

        return view('home.default', $commonData);
    }

    /**
     * Get custom head HTML, Used in ajax calls to show in editor.
     */
    public function customHeadContent()
    {
        return view('common.custom-head');
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
