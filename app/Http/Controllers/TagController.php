<?php namespace BookStack\Http\Controllers;

use BookStack\Actions\TagRepo;
use Illuminate\Http\Request;

class TagController extends Controller
{

    protected $tagRepo;

    /**
     * TagController constructor.
     * @param $tagRepo
     */
    public function __construct(TagRepo $tagRepo)
    {
        $this->tagRepo = $tagRepo;
        parent::__construct();
    }

    /**
     * Get all the Tags for a particular entity
     * @param $entityType
     * @param $entityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForEntity($entityType, $entityId)
    {
        $tags = $this->tagRepo->getForEntity($entityType, $entityId);
        return response()->json($tags);
    }

    /**
     * Get tag name suggestions from a given search term.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNameSuggestions(Request $request)
    {
        $searchTerm = $request->get('search', false);
        $suggestions = $this->tagRepo->getNameSuggestions($searchTerm);
        return response()->json($suggestions);
    }

    /**
     * Get tag value suggestions from a given search term.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getValueSuggestions(Request $request)
    {
        $searchTerm = $request->get('search', false);
        $tagName = $request->get('name', false);
        $suggestions = $this->tagRepo->getValueSuggestions($searchTerm, $tagName);
        return response()->json($suggestions);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        $tags = $this->tagRepo->getForIndex(false);

        return view('tags/index', [
            'tags' => $tags
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search($searchTerm){
        $tags = $this->tagRepo->getForIndex($searchTerm);

        return view('tags/search', [
            'tags' => $tags,
            'searchTerm' => $searchTerm
        ]);

    }

}
