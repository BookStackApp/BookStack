<?php namespace BookStack\Repos;

use BookStack\Attribute;
use BookStack\Entity;
use BookStack\Services\PermissionService;

/**
 * Class AttributeRepo
 * @package BookStack\Repos
 */
class AttributeRepo
{

    protected $attribute;
    protected $entity;
    protected $permissionService;

    /**
     * AttributeRepo constructor.
     * @param Attribute $attr
     * @param Entity $ent
     * @param PermissionService $ps
     */
    public function __construct(Attribute $attr, Entity $ent, PermissionService $ps)
    {
        $this->attribute = $attr;
        $this->entity = $ent;
        $this->permissionService = $ps;
    }


}