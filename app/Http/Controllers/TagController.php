<?php namespace BookStack\Http\Controllers;

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
        parent::__construct();
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
