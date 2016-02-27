<?php

namespace BookStack\Http\Controllers;

use BookStack\Permission;
use BookStack\Role;
use Illuminate\Http\Request;
use BookStack\Http\Requests;

class PermissionController extends Controller
{

    protected $role;
    protected $permission;

    /**
     * PermissionController constructor.
     * @param Role $role
     * @param Permission $permission
     * @internal param $user
     */
    public function __construct(Role $role, Permission $permission)
    {
        $this->role = $role;
        $this->permission = $permission;
        parent::__construct();
    }

    /**
     * Show a listing of the roles in the system.
     */
    public function listRoles()
    {
        $this->checkPermission('user-roles-manage');
        $roles = $this->role->all();
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

        $role = $this->role->newInstance($request->all());
        $role->name = str_replace(' ', '-', strtolower($request->get('display_name')));
        // Prevent duplicate names
        while ($this->role->where('name', '=', $role->name)->count() > 0) {
            $role->name .= strtolower(str_random(2));
        }
        $role->save();

        if ($request->has('permissions')) {
            $permissionsNames = array_keys($request->get('permissions'));
            $permissions = $this->permission->whereIn('name', $permissionsNames)->pluck('id')->toArray();
            $role->permissions()->sync($permissions);
        } else {
            $role->permissions()->sync([]);
        }

        session()->flash('success', 'Role successfully created');
        return redirect('/settings/roles');
    }

    /**
     * Show the form for editing a user role.
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editRole($id)
    {
        $this->checkPermission('user-roles-manage');
        $role = $this->role->findOrFail($id);
        return view('settings/roles/edit', ['role' => $role]);
    }

    /**
     * Updates a user role.
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateRole($id, Request $request)
    {
        $this->checkPermission('user-roles-manage');
        $this->validate($request, [
            'display_name' => 'required|min:3|max:200',
            'description' => 'max:250'
        ]);

        $role = $this->role->findOrFail($id);
        if ($request->has('permissions')) {
            $permissionsNames = array_keys($request->get('permissions'));
            $permissions = $this->permission->whereIn('name', $permissionsNames)->pluck('id')->toArray();
            $role->permissions()->sync($permissions);
        } else {
            $role->permissions()->sync([]);
        }

        // Ensure admin account always has all permissions
        if ($role->name === 'admin') {
            $permissions = $this->permission->all()->pluck('id')->toArray();
            $role->permissions()->sync($permissions);
        }

        $role->fill($request->all());
        $role->save();

        session()->flash('success', 'Role successfully updated');
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
        $role = $this->role->findOrFail($id);
        $roles = $this->role->where('id', '!=', $id)->get();
        $blankRole = $this->role->newInstance(['display_name' => 'Don\'t migrate users']);
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
        $role = $this->role->findOrFail($id);

        // Prevent deleting admin role
        if ($role->name === 'admin') {
            session()->flash('error', 'The admin role cannot be deleted');
            return redirect()->back();
        }

        if ($role->id == \Setting::get('registration-role')) {
            session()->flash('error', 'This role cannot be deleted while set as the default registration role.');
            return redirect()->back();
        }

        if ($request->has('migration_role_id')) {
            $newRole = $this->role->find($request->get('migration_role_id'));
            if ($newRole) {
                $users = $role->users->pluck('id')->toArray();
                $newRole->users()->sync($users);
            }
        }

        $role->delete();

        session()->flash('success', 'Role successfully deleted');
        return redirect('/settings/roles');
    }
}
