<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Entities\Repos\EntityRepo;
use Illuminate\Http\Response;
use Views;

class HomeController extends Controller
{
    protected $entityRepo;

    /**
     * HomeController constructor.
     * @param EntityRepo $entityRepo
     */
    public function __construct(EntityRepo $entityRepo)
    {
        $this->entityRepo = $entityRepo;
        parent::__construct();
    }


    /**
     * Display the homepage.
     * @return Response
     */
    public function index()
    {
        $activity = Activity::latest(10);
        $draftPages = $this->signedIn ? $this->entityRepo->getUserDraftPages(6) : [];
        $recentFactor = count($draftPages) > 0 ? 0.5 : 1;
        $recents = $this->signedIn ? Views::getUserRecentlyViewed(12*$recentFactor, 0) : $this->entityRepo->getRecentlyCreated('book', 12*$recentFactor);
        $recentlyUpdatedPages = $this->entityRepo->getRecentlyUpdated('page', 12);

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

        if ($homepageOption === 'bookshelves') {
            $shelves = $this->entityRepo->getAllPaginated('bookshelf', 18);
            $shelvesViewType = setting()->getUser($this->currentUser, 'bookshelves_view_type', config('app.views.bookshelves', 'grid'));
            $data = array_merge($commonData, ['shelves' => $shelves, 'shelvesViewType' => $shelvesViewType]);
            return view('common.home-shelves', $data);
        }

        if ($homepageOption === 'books') {
            $books = $this->entityRepo->getAllPaginated('book', 18);
            $booksViewType = setting()->getUser($this->currentUser, 'books_view_type', config('app.views.books', 'list'));
            $data = array_merge($commonData, ['books' => $books, 'booksViewType' => $booksViewType]);
            return view('common.home-book', $data);
        }

        if ($homepageOption === 'page') {
            $homepageSetting = setting('app-homepage', '0:');
            $id = intval(explode(':', $homepageSetting)[0]);
            $customHomepage = $this->entityRepo->getById('page', $id, false, true);
            $this->entityRepo->renderPage($customHomepage, true);
            return view('common.home-custom', array_merge($commonData, ['customHomepage' => $customHomepage]));
        }

        return view('common.home', $commonData);
    }

    /**
     * Get a js representation of the current translations
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getTranslations()
    {
        $locale = app()->getLocale();
        $cacheKey = 'GLOBAL_TRANSLATIONS_' . $locale;

        if (cache()->has($cacheKey) && config('app.env') !== 'development') {
            $resp = cache($cacheKey);
        } else {
            $translations = [
                // Get only translations which might be used in JS
                'common' => trans('common'),
                'components' => trans('components'),
                'entities' => trans('entities'),
                'errors' => trans('errors')
            ];
            $resp = 'window.translations = ' . json_encode($translations);
            cache()->put($cacheKey, $resp, 120);
        }

        return response($resp, 200, [
            'Content-Type' => 'application/javascript'
        ]);
    }

    /**
     * Get custom head HTML, Used in ajax calls to show in editor.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function customHeadContent()
    {
        return view('partials/custom-head-content');
    }

    /**
     * Show the view for /robots.txt
     * @return $this
     */
    public function getRobots()
    {
        $sitePublic = setting('app-public', false);
        $allowRobots = config('app.allow_robots');
        if ($allowRobots === null) {
            $allowRobots = $sitePublic;
        }
        return response()
            ->view('common/robots', ['allowRobots' => $allowRobots])
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Show the route for 404 responses.
     */
    public function getNotFound()
    {
        return response()->view('errors/404', [], 404);
    }
}
