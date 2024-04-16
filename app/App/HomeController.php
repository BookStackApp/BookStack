<?php

namespace BookStack\App;

use BookStack\Activity\ActivityQueries;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\EntityQueries;
use BookStack\Entities\Queries\QueryRecentlyViewed;
use BookStack\Entities\Queries\QueryTopFavourites;
use BookStack\Entities\Tools\PageContent;
use BookStack\Http\Controller;
use BookStack\Util\SimpleListOptions;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected EntityQueries $queries,
    ) {
    }

    /**
     * Display the homepage.
     */
    public function index(
        Request $request,
        ActivityQueries $activities,
        QueryRecentlyViewed $recentlyViewed,
        QueryTopFavourites $topFavourites,
    ) {
        $activity = $activities->latest(10);
        $draftPages = [];

        if ($this->isSignedIn()) {
            $draftPages = $this->queries->pages->currentUserDraftsForList()
                ->orderBy('updated_at', 'desc')
                ->with('book')
                ->take(6)
                ->get();
        }

        $recentFactor = count($draftPages) > 0 ? 0.5 : 1;
        $recents = $this->isSignedIn() ?
            $recentlyViewed->run(12 * $recentFactor, 1)
            : $this->queries->books->visibleForList()->orderBy('created_at', 'desc')->take(12 * $recentFactor)->get();
        $favourites = $topFavourites->run(6);
        $recentlyUpdatedPages = $this->queries->pages->visibleForList()
            ->where('draft', false)
            ->orderBy('updated_at', 'desc')
            ->take($favourites->count() > 0 ? 5 : 10)
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
            $listOptions = SimpleListOptions::fromRequest($request, $key)->withSortOptions([
                'name' => trans('common.sort_name'),
                'created_at' => trans('common.sort_created_at'),
                'updated_at' => trans('common.sort_updated_at'),
            ]);

            $commonData = array_merge($commonData, [
                'view'        => $view,
                'listOptions' => $listOptions,
            ]);
        }

        if ($homepageOption === 'bookshelves') {
            $shelves = $this->queries->shelves->visibleForListWithCover()
                ->orderBy($commonData['listOptions']->getSort(), $commonData['listOptions']->getOrder())
                ->paginate(18);
            $data = array_merge($commonData, ['shelves' => $shelves]);

            return view('home.shelves', $data);
        }

        if ($homepageOption === 'books') {
            $books = $this->queries->books->visibleForListWithCover()
                ->orderBy($commonData['listOptions']->getSort(), $commonData['listOptions']->getOrder())
                ->paginate(18);
            $data = array_merge($commonData, ['books' => $books]);

            return view('home.books', $data);
        }

        if ($homepageOption === 'page') {
            $homepageSetting = setting('app-homepage', '0:');
            $id = intval(explode(':', $homepageSetting)[0]);
            /** @var Page $customHomepage */
            $customHomepage = $this->queries->pages->start()->where('draft', '=', false)->findOrFail($id);
            $pageContent = new PageContent($customHomepage);
            $customHomepage->html = $pageContent->render(false);

            return view('home.specific-page', array_merge($commonData, ['customHomepage' => $customHomepage]));
        }

        return view('home.default', $commonData);
    }
}
