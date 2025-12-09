<?php

namespace BookStack\Users\Controllers;

use BookStack\Http\Controller;
use BookStack\Permissions\Permission;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    /**
     * Search users in the system, with the response formatted
     * for use in a select-style list.
     */
    public function forSelect(Request $request)
    {
        $hasPermission = !user()->isGuest() && (
            userCan(Permission::UsersManage)
                || userCan(Permission::RestrictionsManageOwn)
                || userCan(Permission::RestrictionsManageAll)
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

        /** @var Collection<User> $users */
        $users = $query->get();

        return view('form.user-select-list', [
            'users' => $users,
        ]);
    }

    /**
     * Search users in the system, with the response formatted
     * for use in a list of mentions.
     */
    public function forMentions(Request $request)
    {
        $hasPermission = !user()->isGuest() && (
                userCan(Permission::CommentCreateAll)
                || userCan(Permission::CommentUpdate)
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

        /** @var Collection<User> $users */
        $users = $query->get();

        return view('form.user-mention-list', [
            'users' => $users,
        ]);
    }
}
