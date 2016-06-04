<?php namespace BookStack\Http\Controllers;

use BookStack\Repos\TagRepo;
use Illuminate\Http\Request;
use BookStack\Http\Requests;

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
    }

    /**
     * Get all the Tags for a particular entity
     * @param $entityType
     * @param $entityId
     */
    public function getForEntity($entityType, $entityId)
    {
        $tags = $this->tagRepo->getForEntity($entityType, $entityId);
        return response()->json($tags);
    }

    /**
     * Update the tags for a particular entity.
     * @param $entityType
     * @param $entityId
     * @param Request $request
     * @return mixed
     */
    public function updateForEntity($entityType, $entityId, Request $request)
    {
        $entity = $this->tagRepo->getEntity($entityType, $entityId, 'update');
        if ($entity === null) return $this->jsonError("Entity not found", 404);

        $inputTags = $request->input('tags');
        $tags = $this->tagRepo->saveTagsToEntity($entity, $inputTags);
        return response()->json([
            'tags' => $tags,
            'message' => 'Tags successfully updated'
        ]);
    }

    /**
     * Get tag name suggestions from a given search term.
     * @param Request $request
     */
    public function getNameSuggestions(Request $request)
    {
        $searchTerm = $request->get('search');
        $suggestions = $this->tagRepo->getNameSuggestions($searchTerm);
        return response()->json($suggestions);
    }

    /**
     * Get tag value suggestions from a given search term.
     * @param Request $request
     */
    public function getValueSuggestions(Request $request)
    {
        $searchTerm = $request->get('search');
        $tagName = $request->has('name') ? $request->get('name') : false;
        $suggestions = $this->tagRepo->getValueSuggestions($searchTerm, $tagName);
        return response()->json($suggestions);
    }

}
