<?php

namespace BookStack\Http\Controllers;

use BookStack\Auth\Permissions\PermissionsRepo;
use BookStack\Auth\Role;
use BookStack\Exceptions\PermissionsException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    protected $permissionsRepo;

    /**
     * PermissionController constructor.
     */
    public function __construct(PermissionsRepo $permissionsRepo)
    {
        $this->permissionsRepo = $permissionsRepo;
    }

    /**
     * Show a listing of the roles in the system.
     */
    public function index()
    {
        $this->checkPermission('user-roles-manage');
        $roles = $this->permissionsRepo->getAllRoles();

        $this->setPageTitle(trans('settings.roles'));

        return view('settings.roles.index', ['roles' => $roles]);
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
     *
     * @throws PermissionsException
     */
    public function edit(string $id)
    {
        $this->checkPermission('user-roles-manage');
        $role = $this->permissionsRepo->getRoleById($id);
        if ($role->hidden) {
            throw new PermissionsException(trans('errors.role_cannot_be_edited'));
        }

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
