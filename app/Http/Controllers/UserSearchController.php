<?php

namespace BookStack\Http\Controllers;

use BookStack\Auth\User;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    /**
     * Search users in the system, with the response formatted
     * for use in a select-style list.
     */
    public function forSelect(Request $request)
    {
        $hasPermission = signedInUser() && (
                   userCan('users-manage')
                || userCan('restrictions-manage-own')
                || userCan('restrictions-manage-all')
        );

        if (!$hasPermission) {
            $this->showPermissionError();
        }

        $search = $request->get('search', '');
        $query = User::query()
            ->orderBy('name', 'asc')
            ->take(20);

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return view('form.user-select-list', [
            'users' => $query->get(),
        ]);
    }
}
