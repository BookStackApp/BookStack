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
use BookStack\Entities\Tools\ShelfContext;
// 
use BookStack\Actions\ActivityType;
use BookStack\Actions\View;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Tools\BookContents;
use BookStack\Entities\Tools\Cloner;
use BookStack\Entities\Tools\HierarchyTransformer;
use BookStack\Entities\Tools\PermissionsUpdater;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Facades\Activity;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;
// 
class HomeController extends Controller
{
    protected $bookRepo;
    protected $entityContextManager;

    public function __construct(ShelfContext $entityContextManager, BookRepo $bookRepo)
    {
        $this->bookRepo = $bookRepo;
        $this->entityContextManager = $entityContextManager;
    }
    /**
     * Display the homepage.
     */
    public function index(ActivityQueries $activities)
    {
        $view = setting()->getForCurrentUser('books_view_type');
        $sort = setting()->getForCurrentUser('books_sort', 'name');
        $order = setting()->getForCurrentUser('books_sort_order', 'asc');

        $books = $this->bookRepo->getAllPaginated(18, $sort, $order);
        //$books = $this->bookRepo->getAllPaginated(18, $sort, $order);
        foreach ($books as $book) {
            
            $books = (new BookContents($book))->getTree(true);
        }
        $recents = $this->isSignedIn() ? $this->bookRepo->getRecentlyViewed(4) : false;
        $popular = $this->bookRepo->getPopular(4);
        $new = $this->bookRepo->getRecentlyCreated(4);

        $this->entityContextManager->clearShelfContext();

        $this->setPageTitle(trans('entities.books'));

        // return view('books.index', [
        //     'books'   => $books,
        //     'recents' => $recents,
        //     'popular' => $popular,
        //     'new'     => $new,
        //     'view'    => $view,
        //     'sort'    => $sort,
        //     'order'   => $order,
        // ]);
        return view('home.default',compact('books'));
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
