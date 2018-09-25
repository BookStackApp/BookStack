<?php namespace BookStack\Http\Controllers;

use BookStack\Exceptions\PermissionsException;
use BookStack\Auth\Permissions\PermissionsRepo;
use Illuminate\Http\Request;

class PermissionController extends Controller
{

    protected $permissionsRepo;

    /**
     * PermissionController constructor.
     * @param \BookStack\Auth\Permissions\PermissionsRepo $permissionsRepo
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
        return view('settings/roles/index', ['roles' => $roles]);
    }

    /**
     * Show the form to create a new role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createRole()
    {
        $this->checkPermission('user-roles-manage');
        return view('settings/roles/create');
    }

    /**
     * Store a new role in the system.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeRole(Request $request)
    {
        $this->checkPermission('user-roles-manage');
        $this->validate($request, [
            'display_name' => 'required|min:3|max:200',
            'description' => 'max:250'
        ]);

        $this->permissionsRepo->saveNewRole($request->all());
        session()->flash('success', trans('settings.role_create_success'));
        return redirect('/settings/roles');
    }

    /**
     * Show the form for editing a user role.
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws PermissionsException
     */
    public function editRole($id)
    {
        $this->checkPermission('user-roles-manage');
        $role = $this->permissionsRepo->getRoleById($id);
        if ($role->hidden) {
            throw new PermissionsException(trans('errors.role_cannot_be_edited'));
        }
        return view('settings/roles/edit', ['role' => $role]);
    }

    /**
     * Updates a user role.
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws PermissionsException
     */
    public function updateRole($id, Request $request)
    {
        $this->checkPermission('user-roles-manage');
        $this->validate($request, [
            'display_name' => 'required|min:3|max:200',
            'description' => 'max:250'
        ]);

        $this->permissionsRepo->updateRole($id, $request->all());
        session()->flash('success', trans('settings.role_update_success'));
        return redirect('/settings/roles');
    }

    /**
     * Show the view to delete a role.
     * Offers the chance to migrate users.
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDeleteRole($id)
    {
        $this->checkPermission('user-roles-manage');
        $role = $this->permissionsRepo->getRoleById($id);
        $roles = $this->permissionsRepo->getAllRolesExcept($role);
        $blankRole = $role->newInstance(['display_name' => trans('settings.role_delete_no_migration')]);
        $roles->prepend($blankRole);
        return view('settings/roles/delete', ['role' => $role, 'roles' => $roles]);
    }

    /**
     * Delete a role from the system,
     * Migrate from a previous role if set.
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteRole($id, Request $request)
    {
        $this->checkPermission('user-roles-manage');

        try {
            $this->permissionsRepo->deleteRole($id, $request->get('migrate_role_id'));
        } catch (PermissionsException $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }

        session()->flash('success', trans('settings.role_delete_success'));
        return redirect('/settings/roles');
    }
}
