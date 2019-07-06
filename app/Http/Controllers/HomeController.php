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

        // Add required list ordering & sorting for books & shelves views.
        if ($homepageOption === 'bookshelves' || $homepageOption === 'books') {
            $key = $homepageOption;
            $view = setting()->getUser($this->currentUser, $key . '_view_type', config('app.views.' . $key));
            $sort = setting()->getUser($this->currentUser, $key . '_sort', 'name');
            $order = setting()->getUser($this->currentUser, $key . '_sort_order', 'asc');

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
            $shelves = $this->entityRepo->getAllPaginated('bookshelf', 18, $commonData['sort'], $commonData['order']);
            foreach ($shelves as $shelf) {
                $shelf->books = $this->entityRepo->getBookshelfChildren($shelf);
            }
            $data = array_merge($commonData, ['shelves' => $shelves]);
            return view('common.home-shelves', $data);
        }

        if ($homepageOption === 'books') {
            $books = $this->entityRepo->getAllPaginated('book', 18, $commonData['sort'], $commonData['order']);
            $data = array_merge($commonData, ['books' => $books]);
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
     * Get custom head HTML, Used in ajax calls to show in editor.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function customHeadContent()
    {
        return view('partials.custom-head-content');
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
