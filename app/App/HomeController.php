<?php

namespace BookStack\App;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Queries\EntityQueries;
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
    ) {
        $homepageType = setting('app-homepage-type', 'default');
        if (!in_array($homepageType, ['default', 'books', 'bookshelves', 'page'])) {
            $homepageType = 'default';
        }

        $commonData = [
            'homeView' => $homepageType,
        ];

        // Add required list ordering & sorting for books & shelves views.
        if ($homepageType === 'bookshelves' || $homepageType === 'books') {
            $key = $homepageType;
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

        if ($homepageType === 'bookshelves') {
            $shelves = $this->queries->shelves->visibleForListWithCover()
                ->orderBy($commonData['listOptions']->getSort(), $commonData['listOptions']->getOrder())
                ->paginate(setting()->getInteger('lists-page-count-shelves', 18, 1, 1000));
            $data = array_merge($commonData, ['shelves' => $shelves]);

            return view('home.shelves', $data);
        }

        if ($homepageType === 'books') {
            $books = $this->queries->books->visibleForListWithCover()
                ->orderBy($commonData['listOptions']->getSort(), $commonData['listOptions']->getOrder())
                ->paginate(setting()->getInteger('lists-page-count-books', 18, 1, 1000));
            $data = array_merge($commonData, ['books' => $books]);

            return view('home.books', $data);
        }

        if ($homepageType === 'page') {
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
