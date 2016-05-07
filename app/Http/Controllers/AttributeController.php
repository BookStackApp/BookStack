<?php namespace BookStack\Http\Controllers;

use BookStack\Repos\AttributeRepo;
use Illuminate\Http\Request;
use BookStack\Http\Requests;

class AttributeController extends Controller
{

    protected $attributeRepo;

    /**
     * AttributeController constructor.
     * @param $attributeRepo
     */
    public function __construct(AttributeRepo $attributeRepo)
    {
        $this->attributeRepo = $attributeRepo;
    }

    /**
     * Get all the Attributes for a particular entity
     * @param $entityType
     * @param $entityId
     */
    public function getForEntity($entityType, $entityId)
    {
        $attributes = $this->attributeRepo->getForEntity($entityType, $entityId);
        return response()->json($attributes);
    }

    /**
     * Update the attributes for a particular entity.
     * @param $entityType
     * @param $entityId
     * @param Request $request
     * @return mixed
     */
    public function updateForEntity($entityType, $entityId, Request $request)
    {

        $this->validate($request, [
            'attributes.*.name' => 'required|min:3|max:250',
            'attributes.*.value' => 'max:250'
        ]);

        $entity = $this->attributeRepo->getEntity($entityType, $entityId, 'update');
        if ($entity === null) return $this->jsonError("Entity not found", 404);

        $inputAttributes = $request->input('attributes');
        $attributes = $this->attributeRepo->saveAttributesToEntity($entity, $inputAttributes);
        return response()->json($attributes);
    }

    /**
     * Get attribute name suggestions from a given search term.
     * @param Request $request
     */
    public function getNameSuggestions(Request $request)
    {
        $searchTerm = $request->get('search');
        $suggestions = $this->attributeRepo->getNameSuggestions($searchTerm);
        return response()->json($suggestions);
    }


}
