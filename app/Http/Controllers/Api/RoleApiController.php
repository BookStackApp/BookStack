<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Auth\Permissions\PermissionsRepo;
use BookStack\Auth\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleApiController extends ApiController
{
    protected PermissionsRepo $permissionsRepo;

    protected array $fieldsToExpose = [
        'display_name', 'description', 'mfa_enforced', 'external_auth_id', 'created_at', 'updated_at',
    ];

    protected $rules = [
        'create' => [
            'display_name'  => ['required', 'min:3', 'max:180'],
            'description'   => ['max:180'],
            'mfa_enforced'  => ['boolean'],
            'external_auth_id' => ['string'],
            'permissions'   => ['array'],
            'permissions.*' => ['string'],
        ],
        'update' => [
            'display_name'  => ['min:3', 'max:180'],
            'description'   => ['max:180'],
            'mfa_enforced'  => ['boolean'],
            'external_auth_id' => ['string'],
            'permissions'   => ['array'],
            'permissions.*' => ['string'],
        ]
    ];

    public function __construct(PermissionsRepo $permissionsRepo)
    {
        $this->permissionsRepo = $permissionsRepo;

        // Checks for all endpoints in this controller
        $this->middleware(function ($request, $next) {
            $this->checkPermission('user-roles-manage');

            return $next($request);
        });
    }

    /**
     * Get a listing of roles in the system.
     * Requires permission to manage roles.
     */
    public function list()
    {
        $roles = Role::query()->select(['*'])
            ->withCount(['users', 'permissions']);

        return $this->apiListingResponse($roles, [
            ...$this->fieldsToExpose,
            'permissions_count',
            'users_count',
        ]);
    }

    /**
     * Create a new role in the system.
     * Requires permission to manage roles.
     */
    public function create(Request $request)
    {
        $data = $this->validate($request, $this->rules()['create']);

        $role = null;
        DB::transaction(function () use ($data, &$role) {
            $role = $this->permissionsRepo->saveNewRole($data);
        });

        $this->singleFormatter($role);

        return response()->json($role);
    }

    /**
     * View the details of a single user.
     * Requires permission to manage roles.
     */
    public function read(string $id)
    {
        $user = $this->permissionsRepo->getRoleById($id);
        $this->singleFormatter($user);

        return response()->json($user);
    }

    /**
     * Update an existing role in the system.
     * Requires permission to manage roles.
     */
    public function update(Request $request, string $id)
    {
        $data = $this->validate($request, $this->rules()['update']);
        $role = $this->permissionsRepo->updateRole($id, $data);

        $this->singleFormatter($role);

        return response()->json($role);
    }

    /**
     * Delete a user from the system.
     * Can optionally accept a user id via `migrate_ownership_id` to indicate
     * who should be the new owner of their related content.
     * Requires permission to manage roles.
     */
    public function delete(string $id)
    {
        $this->permissionsRepo->deleteRole(intval($id));

        return response('', 204);
    }

    /**
     * Format the given role model for single-result display.
     */
    protected function singleFormatter(Role $role)
    {
        $role->load('users:id,name,slug');
        $role->unsetRelation('permissions');
        $role->setAttribute('permissions', $role->permissions()->orderBy('name', 'asc')->pluck('name'));
        $role->makeVisible(['users', 'permissions']);
    }
}
