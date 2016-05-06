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
        
    }
}
