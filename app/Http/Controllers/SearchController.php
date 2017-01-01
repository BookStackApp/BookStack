<?php namespace BookStack\Http\Controllers;

use BookStack\Repos\EntityRepo;
use BookStack\Services\ViewService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $entityRepo;
    protected $viewService;

    /**
     * SearchController constructor.
     * @param EntityRepo $entityRepo
     * @param ViewService $viewService
     */
    public function __construct(EntityRepo $entityRepo, ViewService $viewService)
    {
        $this->entityRepo = $entityRepo;
        $this->viewService = $viewService;
        parent::__construct();
    }

    /**
     * Searches all entities.
     * @param Request $request
     * @return \Illuminate\View\View
     * @internal param string $searchTerm
     */
    public function searchAll(Request $request)
    {
        if (!$request->has('term')) {
            return redirect()->back();
        }
        $searchTerm = $request->get('term');
        $paginationAppends = $request->only('term');
        $pages = $this->entityRepo->getBySearch('page', $searchTerm, [], 20, $paginationAppends);
        $books = $this->entityRepo->getBySearch('book', $searchTerm, [], 10, $paginationAppends);
        $chapters = $this->entityRepo->getBySearch('chapter', $searchTerm, [], 10, $paginationAppends);
        $this->setPageTitle(trans('entities.search_for_term', ['term' => $searchTerm]));
        return view('search/all', [
            'pages'      => $pages,
            'books'      => $books,
            'chapters'   => $chapters,
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Search only the pages in the system.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function searchPages(Request $request)
    {
        if (!$request->has('term')) return redirect()->back();

        $searchTerm = $request->get('term');
        $paginationAppends = $request->only('term');
        $pages = $this->entityRepo->getBySearch('page', $searchTerm, [], 20, $paginationAppends);
        $this->setPageTitle(trans('entities.search_page_for_term', ['term' => $searchTerm]));
        return view('search/entity-search-list', [
            'entities'   => $pages,
            'title'      => trans('entities.search_results_page'),
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Search only the chapters in the system.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function searchChapters(Request $request)
    {
        if (!$request->has('term')) return redirect()->back();

        $searchTerm = $request->get('term');
        $paginationAppends = $request->only('term');
        $chapters = $this->entityRepo->getBySearch('chapter', $searchTerm, [], 20, $paginationAppends);
        $this->setPageTitle(trans('entities.search_chapter_for_term', ['term' => $searchTerm]));
        return view('search/entity-search-list', [
            'entities'   => $chapters,
            'title'      => trans('entities.search_results_chapter'),
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Search only the books in the system.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function searchBooks(Request $request)
    {
        if (!$request->has('term')) return redirect()->back();

        $searchTerm = $request->get('term');
        $paginationAppends = $request->only('term');
        $books = $this->entityRepo->getBySearch('book', $searchTerm, [], 20, $paginationAppends);
        $this->setPageTitle(trans('entities.search_book_for_term', ['term' => $searchTerm]));
        return view('search/entity-search-list', [
            'entities'   => $books,
            'title'      => trans('entities.search_results_book'),
            'searchTerm' => $searchTerm
        ]);
    }

    /**
     * Searches all entities within a book.
     * @param Request $request
     * @param integer $bookId
     * @return \Illuminate\View\View
     * @internal param string $searchTerm
     */
    public function searchBook(Request $request, $bookId)
    {
        if (!$request->has('term')) {
            return redirect()->back();
        }
        $searchTerm = $request->get('term');
        $searchWhereTerms = [['book_id', '=', $bookId]];
        $pages = $this->entityRepo->getBySearch('page', $searchTerm, $searchWhereTerms);
        $chapters = $this->entityRepo->getBySearch('chapter', $searchTerm, $searchWhereTerms);
        return view('search/book', ['pages' => $pages, 'chapters' => $chapters, 'searchTerm' => $searchTerm]);
    }


    /**
     * Search for a list of entities and return a partial HTML response of matching entities.
     * Returns the most popular entities if no search is provided.
     * @param Request $request
     * @return mixed
     */
    public function searchEntitiesAjax(Request $request)
    {
        $entities = collect();
        $entityTypes = $request->has('types') ? collect(explode(',', $request->get('types'))) : collect(['page', 'chapter', 'book']);
        $searchTerm = ($request->has('term') && trim($request->get('term')) !== '') ? $request->get('term') : false;

        // Search for entities otherwise show most popular
        if ($searchTerm !== false) {
            foreach (['page', 'chapter', 'book'] as $entityType) {
                if ($entityTypes->contains($entityType)) {
                    $entities = $entities->merge($this->entityRepo->getBySearch($entityType, $searchTerm)->items());
                }
            }
            $entities = $entities->sortByDesc('title_relevance');
        } else {
            $entityNames = $entityTypes->map(function ($type) {
                return 'BookStack\\' . ucfirst($type);
            })->toArray();
            $entities = $this->viewService->getPopular(20, 0, $entityNames);
        }

        return view('search/entity-ajax-list', ['entities' => $entities]);
    }

}


