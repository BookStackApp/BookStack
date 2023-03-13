<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Tools\PermissionsUpdater;
use Illuminate\Http\Request;

class ContentPermissionsController extends ApiController
{
    public function __construct(
        protected PermissionsUpdater $permissionsUpdater,
        protected EntityProvider $entities
    ) {
    }

    protected $rules = [
        'update' => [
            'owner_id'  => ['int'],

            'override_role_permissions' => ['array'],
            'override_role_permissions.*.role_id' => ['required', 'int'],
            'override_role_permissions.*.view' => ['required', 'boolean'],
            'override_role_permissions.*.create' => ['required', 'boolean'],
            'override_role_permissions.*.update' => ['required', 'boolean'],
            'override_role_permissions.*.delete' => ['required', 'boolean'],

            'override_fallback_permissions' => ['nullable'],
            'override_fallback_permissions.view' => ['required', 'boolean'],
            'override_fallback_permissions.create' => ['required', 'boolean'],
            'override_fallback_permissions.update' => ['required', 'boolean'],
            'override_fallback_permissions.delete' => ['required', 'boolean'],
        ]
    ];

    /**
     * Read the configured content-level permissions for the item of the given type and ID.
     * 'contentType' should be one of: page, book, chapter, bookshelf.
     * 'contentId' should be the relevant ID of that item type you'd like to handle permissions for.
     */
    public function read(string $contentType, string $contentId)
    {
        $entity = $this->entities->get($contentType)
            ->newQuery()->scopes(['visible'])->findOrFail($contentId);

        $this->checkOwnablePermission('restrictions-manage', $entity);

        return response()->json($this->formattedPermissionDataForEntity($entity));
    }

    /**
     * Update the configured content-level permissions for the item of the given type and ID.
     * 'contentType' should be one of: page, book, chapter, bookshelf.
     * 'contentId' should be the relevant ID of that item type you'd like to handle permissions for.
     */
    public function update(Request $request, string $contentType, string $contentId)
    {
        $entity = $this->entities->get($contentType)
            ->newQuery()->scopes(['visible'])->findOrFail($contentId);

        $this->checkOwnablePermission('restrictions-manage', $entity);

        $data = $this->validate($request, $this->rules()['update']);
        $this->permissionsUpdater->updateFromApiRequestData($entity, $data);

        return response()->json($this->formattedPermissionDataForEntity($entity));
    }

    protected function formattedPermissionDataForEntity(Entity $entity): array
    {
        $rolePermissions = $entity->permissions()
            ->where('role_id', '!=', 0)
            ->with(['role:id,display_name'])
            ->get();

        $fallback = $entity->permissions()->where('role_id', '=', 0)->first();
        $fallback?->makeHidden('role_id');

        return [
            'owner' => $entity->ownedBy()->first(),
            'override_role_permissions' => $rolePermissions,
            'override_fallback_permissions' => $fallback,
            'inheriting' => is_null($fallback),
        ];
    }
}
