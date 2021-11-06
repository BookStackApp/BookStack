<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\TagRepo;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagRepo;

    /**
     * TagController constructor.
     */
    public function __construct(TagRepo $tagRepo)
    {
        $this->tagRepo = $tagRepo;
    }

    /**
     * Show a listing of existing tags in the system.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $nameFilter = $request->get('name', '');
        $tags = $this->tagRepo
            ->queryWithTotals($search, $nameFilter)
            ->paginate(50)
            ->appends(array_filter([
                'search' => $search,
                'name'   => $nameFilter,
            ]));

        return view('tags.index', [
            'tags'       => $tags,
            'search'     => $search,
            'nameFilter' => $nameFilter,
        ]);
    }

    /**
     * Get tag name suggestions from a given search term.
     */
    public function getNameSuggestions(Request $request)
    {
        $searchTerm = $request->get('search', null);
        $suggestions = $this->tagRepo->getNameSuggestions($searchTerm);

        return response()->json($suggestions);
    }

    /**
     * Get tag value suggestions from a given search term.
     */
    public function getValueSuggestions(Request $request)
    {
        $searchTerm = $request->get('search', null);
        $tagName = $request->get('name', null);
        $suggestions = $this->tagRepo->getValueSuggestions($searchTerm, $tagName);

        return response()->json($suggestions);
    }
}
