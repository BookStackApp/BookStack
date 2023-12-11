<?php

namespace BookStack\Users\Controllers;

use BookStack\Exceptions\PermissionsException;
use BookStack\Http\Controller;
use BookStack\Permissions\PermissionsRepo;
use BookStack\Users\Models\Role;
use BookStack\Users\Queries\RolesAllPaginatedAndSorted;
use BookStack\Util\SimpleListOptions;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        protected PermissionsRepo $permissionsRepo
    ) {
    }

    /**
     * Show a listing of the roles in the system.
     */
    public function index(Request $request)
    {
        $this->checkPermission('user-roles-manage');

        $listOptions = SimpleListOptions::fromRequest($request, 'roles')->withSortOptions([
            'display_name' => trans('common.sort_name'),
            'users_count' => trans('settings.roles_assigned_users'),
            'permissions_count' => trans('settings.roles_permissions_provided'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ]);

        $roles = (new RolesAllPaginatedAndSorted())->run(20, $listOptions);
        $roles->appends($listOptions->getPaginationAppends());

        $this->setPageTitle(trans('settings.roles'));

        return view('settings.roles.index', [
            'roles'       => $roles,
            'listOptions' => $listOptions,
        ]);
    }

    /**
     * Show the form to create a new role.
     */
    public function create(Request $request)
    {
        $this->checkPermission('user-roles-manage');

        /** @var ?Role $role */
        $role = null;
        if ($request->has('copy_from')) {
            $role = Role::query()->find($request->get('copy_from'));
        }

        if ($role) {
            $role->display_name .= ' (' . trans('common.copy') . ')';
        }

        $this->setPageTitle(trans('settings.role_create'));

        return view('settings.roles.create', ['role' => $role]);
    }

    /**
     * Store a new role in the system.
     */
    public function store(Request $request)
    {
        $this->checkPermission('user-roles-manage');
        $data = $this->validate($request, [
            'display_name' => ['required', 'min:3', 'max:180'],
            'description'  => ['max:180'],
            'external_auth_id' => ['string'],
            'permissions'  => ['array'],
            'mfa_enforced' => ['string'],
        ]);

        $data['permissions'] = array_keys($data['permissions'] ?? []);
        $data['mfa_enforced'] = ($data['mfa_enforced'] ?? 'false') === 'true';
        $this->permissionsRepo->saveNewRole($data);

        return redirect('/settings/roles');
    }

    /**
     * Show the form for editing a user role.
     */
    public function edit(string $id)
    {
        $this->checkPermission('user-roles-manage');
        $role = $this->permissionsRepo->getRoleById($id);

        $this->setPageTitle(trans('settings.role_edit'));

        return view('settings.roles.edit', ['role' => $role]);
    }

    /**
     * Updates a user role.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('user-roles-manage');
        $data = $this->validate($request, [
            'display_name' => ['required', 'min:3', 'max:180'],
            'description'  => ['max:180'],
            'external_auth_id' => ['string'],
            'permissions'  => ['array'],
            'mfa_enforced' => ['string'],
        ]);

        $data['permissions'] = array_keys($data['permissions'] ?? []);
        $data['mfa_enforced'] = ($data['mfa_enforced'] ?? 'false') === 'true';
        $this->permissionsRepo->updateRole($id, $data);

        return redirect('/settings/roles');
    }

    /**
     * Show the view to delete a role.
     * Offers the chance to migrate users.
     */
    public function showDelete(string $id)
    {
        $this->checkPermission('user-roles-manage');
        $role = $this->permissionsRepo->getRoleById($id);
        $roles = $this->permissionsRepo->getAllRolesExcept($role);
        $blankRole = $role->newInstance(['display_name' => trans('settings.role_delete_no_migration')]);
        $roles->prepend($blankRole);

        $this->setPageTitle(trans('settings.role_delete'));

        return view('settings.roles.delete', ['role' => $role, 'roles' => $roles]);
    }

    /**
     * Delete a role from the system,
     * Migrate from a previous role if set.
     *
     * @throws Exception
     */
    public function delete(Request $request, string $id)
    {
        $this->checkPermission('user-roles-manage');

        try {
            $migrateRoleId = intval($request->get('migrate_role_id') ?: "0");
            $this->permissionsRepo->deleteRole($id, $migrateRoleId);
        } catch (PermissionsException $e) {
            $this->showErrorNotification($e->getMessage());

            return redirect("/settings/roles/delete/{$id}");
        }

        return redirect('/settings/roles');
    }
}
