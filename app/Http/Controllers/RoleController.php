<?php

namespace BookStack\Http\Controllers;

use BookStack\Auth\Permissions\PermissionsRepo;
use BookStack\Auth\Queries\RolesAllPaginatedAndSorted;
use BookStack\Auth\Role;
use BookStack\Exceptions\PermissionsException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    protected PermissionsRepo $permissionsRepo;

    public function __construct(PermissionsRepo $permissionsRepo)
    {
        $this->permissionsRepo = $permissionsRepo;
    }

    /**
     * Show a listing of the roles in the system.
     */
    public function index(Request $request)
    {
        $this->checkPermission('user-roles-manage');

        $listDetails = [
            'search' => $request->get('search', ''),
            'sort'   => setting()->getForCurrentUser('roles_sort', 'display_name'),
            'order'  => setting()->getForCurrentUser('roles_sort_order', 'asc'),
        ];

        $roles = (new RolesAllPaginatedAndSorted())->run(20, $listDetails);
        $roles->appends(['search' => $listDetails['search']]);

        $this->setPageTitle(trans('settings.roles'));

        return view('settings.roles.index', [
            'roles'       => $roles,
            'listDetails' => $listDetails,
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
        $this->validate($request, [
            'display_name' => ['required', 'min:3', 'max:180'],
            'description'  => ['max:180'],
        ]);

        $this->permissionsRepo->saveNewRole($request->all());
        $this->showSuccessNotification(trans('settings.role_create_success'));

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
     *
     * @throws ValidationException
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('user-roles-manage');
        $this->validate($request, [
            'display_name' => ['required', 'min:3', 'max:180'],
            'description'  => ['max:180'],
        ]);

        $this->permissionsRepo->updateRole($id, $request->all());
        $this->showSuccessNotification(trans('settings.role_update_success'));

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
            $this->permissionsRepo->deleteRole($id, $request->get('migrate_role_id'));
        } catch (PermissionsException $e) {
            $this->showErrorNotification($e->getMessage());

            return redirect()->back();
        }

        $this->showSuccessNotification(trans('settings.role_delete_success'));

        return redirect('/settings/roles');
    }
}
