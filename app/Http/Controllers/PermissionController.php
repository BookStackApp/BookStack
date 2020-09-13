<?php namespace BookStack\Http\Controllers;

use BookStack\Auth\Permissions\PermissionsRepo;
use BookStack\Exceptions\PermissionsException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PermissionController extends Controller
{

    protected $permissionsRepo;

    /**
     * PermissionController constructor.
     */
    public function __construct(PermissionsRepo $permissionsRepo)
    {
        $this->permissionsRepo = $permissionsRepo;
        parent::__construct();
    }

    /**
     * Show a listing of the roles in the system.
     */
    public function listRoles()
    {
        $this->checkPermission('user-roles-manage');
        $roles = $this->permissionsRepo->getAllRoles();
        return view('settings.roles.index', ['roles' => $roles]);
    }

    /**
     * Show the form to create a new role
     */
    public function createRole()
    {
        $this->checkPermission('user-roles-manage');
        return view('settings.roles.create');
    }

    /**
     * Store a new role in the system.
     */
    public function storeRole(Request $request)
    {
        $this->checkPermission('user-roles-manage');
        $this->validate($request, [
            'display_name' => 'required|min:3|max:180',
            'description' => 'max:180'
        ]);

        $this->permissionsRepo->saveNewRole($request->all());
        $this->showSuccessNotification(trans('settings.role_create_success'));
        return redirect('/settings/roles');
    }

    /**
     * Show the form for editing a user role.
     * @throws PermissionsException
     */
    public function editRole(string $id)
    {
        $this->checkPermission('user-roles-manage');
        $role = $this->permissionsRepo->getRoleById($id);
        if ($role->hidden) {
            throw new PermissionsException(trans('errors.role_cannot_be_edited'));
        }
        return view('settings.roles.edit', ['role' => $role]);
    }

    /**
     * Updates a user role.
     * @throws ValidationException
     */
    public function updateRole(Request $request, string $id)
    {
        $this->checkPermission('user-roles-manage');
        $this->validate($request, [
            'display_name' => 'required|min:3|max:180',
            'description' => 'max:180'
        ]);

        $this->permissionsRepo->updateRole($id, $request->all());
        $this->showSuccessNotification(trans('settings.role_update_success'));
        return redirect('/settings/roles');
    }

    /**
     * Show the view to delete a role.
     * Offers the chance to migrate users.
     */
    public function showDeleteRole(string $id)
    {
        $this->checkPermission('user-roles-manage');
        $role = $this->permissionsRepo->getRoleById($id);
        $roles = $this->permissionsRepo->getAllRolesExcept($role);
        $blankRole = $role->newInstance(['display_name' => trans('settings.role_delete_no_migration')]);
        $roles->prepend($blankRole);
        return view('settings.roles.delete', ['role' => $role, 'roles' => $roles]);
    }

    /**
     * Delete a role from the system,
     * Migrate from a previous role if set.
     * @throws Exception
     */
    public function deleteRole(Request $request, string $id)
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
