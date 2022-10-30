<?php

namespace BookStack\Http\Controllers;

use BookStack\Auth\UserRepo;
use Illuminate\Http\Request;

class UserPreferencesController extends Controller
{
    protected UserRepo $userRepo;

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Update the user's preferred book-list display setting.
     */
    public function switchBooksView(Request $request, int $id)
    {
        return $this->switchViewType($id, $request, 'books');
    }

    /**
     * Update the user's preferred shelf-list display setting.
     */
    public function switchShelvesView(Request $request, int $id)
    {
        return $this->switchViewType($id, $request, 'bookshelves');
    }

    /**
     * Update the user's preferred shelf-view book list display setting.
     */
    public function switchShelfView(Request $request, int $id)
    {
        return $this->switchViewType($id, $request, 'bookshelf');
    }

    /**
     * For a type of list, switch with stored view type for a user.
     */
    protected function switchViewType(int $userId, Request $request, string $listName)
    {
        $this->checkPermissionOrCurrentUser('users-manage', $userId);

        $viewType = $request->get('view_type');
        if (!in_array($viewType, ['grid', 'list'])) {
            $viewType = 'list';
        }

        $user = $this->userRepo->getById($userId);
        $key = $listName . '_view_type';
        setting()->putUser($user, $key, $viewType);

        return redirect()->back(302, [], "/settings/users/$userId");
    }

    /**
     * Change the stored sort type for a particular view.
     */
    public function changeSort(Request $request, string $id, string $type)
    {
        $validSortTypes = ['books', 'bookshelves', 'shelf_books', 'users', 'roles', 'webhooks'];
        if (!in_array($type, $validSortTypes)) {
            return redirect()->back(500);
        }

        $this->checkPermissionOrCurrentUser('users-manage', $id);

        $sort = substr($request->get('sort') ?: 'name', 0, 50);
        $order = $request->get('order') === 'desc' ? 'desc' : 'asc';

        $user = $this->userRepo->getById($id);
        $sortKey = $type . '_sort';
        $orderKey = $type . '_sort_order';
        setting()->putUser($user, $sortKey, $sort);
        setting()->putUser($user, $orderKey, $order);

        return redirect()->back(302, [], "/settings/users/{$id}");
    }

    /**
     * Toggle dark mode for the current user.
     */
    public function toggleDarkMode()
    {
        $enabled = setting()->getForCurrentUser('dark-mode-enabled', false);
        setting()->putUser(user(), 'dark-mode-enabled', $enabled ? 'false' : 'true');

        return redirect()->back();
    }

    /**
     * Update the stored section expansion preference for the given user.
     */
    public function updateExpansionPreference(Request $request, string $id, string $key)
    {
        $this->checkPermissionOrCurrentUser('users-manage', $id);
        $keyWhitelist = ['home-details'];
        if (!in_array($key, $keyWhitelist)) {
            return response('Invalid key', 500);
        }

        $newState = $request->get('expand', 'false');

        $user = $this->userRepo->getById($id);
        setting()->putUser($user, 'section_expansion#' . $key, $newState);

        return response('', 204);
    }

    public function updateCodeLanguageFavourite(Request $request)
    {
        $validated = $this->validate($request, [
            'language' => ['required', 'string', 'max:20'],
            'active'   => ['required', 'bool'],
        ]);

        $currentFavoritesStr = setting()->getForCurrentUser('code-language-favourites', '');
        $currentFavorites = array_filter(explode(',', $currentFavoritesStr));

        $isFav = in_array($validated['language'], $currentFavorites);
        if (!$isFav && $validated['active']) {
            $currentFavorites[] = $validated['language'];
        } elseif ($isFav && !$validated['active']) {
            $index = array_search($validated['language'], $currentFavorites);
            array_splice($currentFavorites, $index, 1);
        }

        setting()->putUser(user(), 'code-language-favourites', implode(',', $currentFavorites));
    }
}
