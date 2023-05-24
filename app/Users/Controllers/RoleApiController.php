<?php

namespace BookStack\Users\Controllers;

use BookStack\Http\ApiController;
use BookStack\Permissions\PermissionsRepo;
use BookStack\Users\Models\Role;
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
            'display_name'  => ['required', 'string', 'min:3', 'max:180'],
            'description'   => ['string', 'max:180'],
            'mfa_enforced'  => ['boolean'],
            'external_auth_id' => ['string'],
            'permissions'   => ['array'],
            'permissions.*' => ['string'],
        ],
        'update' => [
            'display_name'  => ['string', 'min:3', 'max:180'],
            'description'   => ['string', 'max:180'],
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
     * Permissions should be provided as an array of permission name strings.
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
     * View the details of a single role.
     * Provides the permissions and a high-level list of the users assigned.
     * Requires permission to manage roles.
     */
    public function read(string $id)
    {
        $role = $this->permissionsRepo->getRoleById($id);
        $this->singleFormatter($role);

        return response()->json($role);
    }

    /**
     * Update an existing role in the system.
     * Permissions should be provided as an array of permission name strings.
     * An empty "permissions" array would clear granted permissions.
     * In many cases, where permissions are changed, you'll want to fetch the existing
     * permissions and then modify before providing in your update request.
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
     * Delete a role from the system.
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
