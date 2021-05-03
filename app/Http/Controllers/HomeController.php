<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Tools\PageContent;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Repos\BookshelfRepo;
use Illuminate\Http\Response;
use Views;

class HomeController extends Controller
{

    /**
     * Display the homepage.
     */
    public function index()
    {
        $activity = Activity::latest(10);
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
              Views::getUserRecentlyViewed(12*$recentFactor, 1)
            : Book::visible()->orderBy('created_at', 'desc')->take(12 * $recentFactor)->get();
        $recentlyUpdatedPages = Page::visible()->with('book')
            ->where('draft', false)
            ->orderBy('updated_at', 'desc')
            ->take(12)
            ->get();

        $homepageOptions = ['default', 'books', 'bookshelves', 'page'];
        $homepageOption = setting('app-homepage-type', 'default');
        if (!in_array($homepageOption, $homepageOptions)) {
            $homepageOption = 'default';
        }

        $commonData = [
            'activity' => $activity,
            'recents' => $recents,
            'recentlyUpdatedPages' => $recentlyUpdatedPages,
            'draftPages' => $draftPages,
        ];

        // Add required list ordering & sorting for books & shelves views.
        if ($homepageOption === 'bookshelves' || $homepageOption === 'books') {
            $key = $homepageOption;
            $view = setting()->getForCurrentUser($key . '_view_type');
            $sort = setting()->getForCurrentUser($key . '_sort', 'name');
            $order = setting()->getForCurrentUser($key . '_sort_order', 'asc');

            $sortOptions = [
                'name' => trans('common.sort_name'),
                'created_at' => trans('common.sort_created_at'),
                'updated_at' => trans('common.sort_updated_at'),
            ];

            $commonData = array_merge($commonData, [
                'view' => $view,
                'sort' => $sort,
                'order' => $order,
                'sortOptions' => $sortOptions,
            ]);
        }

        if ($homepageOption === 'bookshelves') {
            $shelves = app(BookshelfRepo::class)->getAllPaginated(18, $commonData['sort'], $commonData['order']);
            $data = array_merge($commonData, ['shelves' => $shelves]);
            return view('common.home-shelves', $data);
        }

        if ($homepageOption === 'books') {
            $bookRepo = app(BookRepo::class);
            $books = $bookRepo->getAllPaginated(18, $commonData['sort'], $commonData['order']);
            $data = array_merge($commonData, ['books' => $books]);
            return view('common.home-book', $data);
        }

        if ($homepageOption === 'page') {
            $homepageSetting = setting('app-homepage', '0:');
            $id = intval(explode(':', $homepageSetting)[0]);
            $customHomepage = Page::query()->where('draft', '=', false)->findOrFail($id);
            $pageContent = new PageContent($customHomepage);
            $customHomepage->html = $pageContent->render(true);
            return view('common.home-custom', array_merge($commonData, ['customHomepage' => $customHomepage]));
        }

        return view('common.home', $commonData);
    }

    /**
     * Get custom head HTML, Used in ajax calls to show in editor.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function customHeadContent()
    {
        return view('partials.custom-head');
    }

    /**
     * Show the view for /robots.txt
     */
    public function getRobots()
    {
        $sitePublic = setting('app-public', false);
        $allowRobots = config('app.allow_robots');

        if ($allowRobots === null) {
            $allowRobots = $sitePublic;
        }

        return response()
            ->view('common.robots', ['allowRobots' => $allowRobots])
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Show the route for 404 responses.
     */
    public function getNotFound()
    {
        return response()->view('errors.404', [], 404);
    }
}
