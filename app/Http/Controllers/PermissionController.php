<?php

namespace BookStack\Http\Controllers;

use BookStack\Role;
use BookStack\User;
use Illuminate\Http\Request;

use BookStack\Http\Requests;
use BookStack\Http\Controllers\Controller;

class PermissionController extends Controller
{

    protected $role;

    /**
     * PermissionController constructor.
     * @param $role
     * @param $user
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
        parent::__construct();
    }

    /**
     * Show a listing of the roles in the system.
     */
    public function listRoles()
    {
        $this->checkPermission('settings-update');
        $roles = $this->role->all();
        return view('settings/roles/index', ['roles' => $roles]);
    }

    /**
     * Show the form for editing a user role.
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editRole($id)
    {
        $this->checkPermission('settings-update');
        $role = $this->role->findOrFail($id);
        return view('settings/roles/edit', ['role' => $role]);
    }
}
