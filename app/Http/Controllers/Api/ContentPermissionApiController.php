<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Tools\PermissionsUpdater;
use Illuminate\Http\Request;

class ContentPermissionApiController extends ApiController
{
    public function __construct(
        protected PermissionsUpdater $permissionsUpdater,
        protected EntityProvider $entities
    ) {
    }

    protected $rules = [
        'update' => [
            'owner_id'  => ['int'],

            'role_permissions' => ['array'],
            'role_permissions.*.role_id' => ['required', 'int', 'exists:roles,id'],
            'role_permissions.*.view' => ['required', 'boolean'],
            'role_permissions.*.create' => ['required', 'boolean'],
            'role_permissions.*.update' => ['required', 'boolean'],
            'role_permissions.*.delete' => ['required', 'boolean'],

            'fallback_permissions' => ['nullable'],
            'fallback_permissions.inheriting' => ['required_with:fallback_permissions', 'boolean'],
            'fallback_permissions.view' => ['required_if:fallback_permissions.inheriting,false', 'boolean'],
            'fallback_permissions.create' => ['required_if:fallback_permissions.inheriting,false', 'boolean'],
            'fallback_permissions.update' => ['required_if:fallback_permissions.inheriting,false', 'boolean'],
            'fallback_permissions.delete' => ['required_if:fallback_permissions.inheriting,false', 'boolean'],
        ]
    ];

    /**
     * Read the configured content-level permissions for the item of the given type and ID.
     * 'contentType' should be one of: page, book, chapter, bookshelf.
     * 'contentId' should be the relevant ID of that item type you'd like to handle permissions for.
     * The permissions shown are those that override the default for just the specified item, they do not show the
     * full evaluated permission for a role, nor do they reflect permissions inherited from other items in the hierarchy.
     * Fallback permission values may be `null` when inheriting is active.
     */
    public function read(string $contentType, string $contentId)
    {
        $entity = $this->entities->get($contentType)
            ->newQuery()->scopes(['visible'])->findOrFail($contentId);

        $this->checkOwnablePermission('restrictions-manage', $entity);

        return response()->json($this->formattedPermissionDataForEntity($entity));
    }

    /**
     * Update the configured content-level permission overrides for the item of the given type and ID.
     * 'contentType' should be one of: page, book, chapter, bookshelf.
     * 'contentId' should be the relevant ID of that item type you'd like to handle permissions for.
     * Providing an empty `role_permissions` array will remove any existing configured role permissions,
     * so you may want to fetch existing permissions beforehand if just adding/removing a single item.
     * You should completely omit the `owner_id`, `role_permissions` and/or the `fallback_permissions` properties
     * from your request data if you don't wish to update details within those categories.
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
        $fallbackData = [
            'inheriting' => is_null($fallback),
            'view' => $fallback->view ?? null,
            'create' => $fallback->create ?? null,
            'update' => $fallback->update ?? null,
            'delete' => $fallback->delete ?? null,
        ];

        return [
            'owner' => $entity->ownedBy()->first(),
            'role_permissions' => $rolePermissions,
            'fallback_permissions' => $fallbackData,
        ];
    }
}
