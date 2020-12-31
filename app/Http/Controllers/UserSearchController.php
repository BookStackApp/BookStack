<?php

namespace BookStack\Http\Controllers;

use BookStack\Auth\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    /**
     * Search users in the system, with the response formatted
     * for use in a select-style list.
     */
    public function forSelect(Request $request)
    {
        $search = $request->get('search', '');
        $query = User::query()->orderBy('name', 'desc')
            ->take(20);

        if (!empty($search)) {
            $query->where(function(Builder $query) use ($search) {
                $query->where('email', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $users = $query->get();
        return view('form.user-select-list', compact('users'));
    }
}
