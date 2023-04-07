<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\TagRepo;
use BookStack\Util\SimpleListOptions;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct(
        protected TagRepo $tagRepo
    ) {
    }

    /**
     * Show a listing of existing tags in the system.
     */
    public function index(Request $request)
    {
        $listOptions = SimpleListOptions::fromRequest($request, 'tags')->withSortOptions([
            'name' => trans('common.sort_name'),
            'usages' => trans('entities.tags_usages'),
        ]);

        $nameFilter = $request->get('name', '');
        $tags = $this->tagRepo
            ->queryWithTotals($listOptions, $nameFilter)
            ->paginate(50)
            ->appends(array_filter(array_merge($listOptions->getPaginationAppends(), [
                'name'   => $nameFilter,
            ])));

        $this->setPageTitle(trans('entities.tags'));

        return view('tags.index', [
            'tags'        => $tags,
            'nameFilter'  => $nameFilter,
            'listOptions' => $listOptions,
        ]);
    }

    /**
     * Get tag name suggestions from a given search term.
     */
    public function getNameSuggestions(Request $request)
    {
        $searchTerm = $request->get('search', '');
        $suggestions = $this->tagRepo->getNameSuggestions($searchTerm);

        return response()->json($suggestions);
    }

    /**
     * Get tag value suggestions from a given search term.
     */
    public function getValueSuggestions(Request $request)
    {
        $searchTerm = $request->get('search', '');
        $tagName = $request->get('name', '');
        $suggestions = $this->tagRepo->getValueSuggestions($searchTerm, $tagName);

        return response()->json($suggestions);
    }
}
