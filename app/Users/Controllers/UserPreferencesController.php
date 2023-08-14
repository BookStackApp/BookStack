<?php

namespace BookStack\Users\Controllers;

use BookStack\Activity\Models\Watch;
use BookStack\Http\Controller;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Settings\UserNotificationPreferences;
use BookStack\Settings\UserShortcutMap;
use BookStack\Users\UserRepo;
use Illuminate\Http\Request;

class UserPreferencesController extends Controller
{
    public function __construct(
        protected UserRepo $userRepo
    ) {
    }

    /**
     * Show the overview for user preferences.
     */
    public function index()
    {
        return view('users.preferences.index');
    }

    /**
     * Show the user-specific interface shortcuts.
     */
    public function showShortcuts()
    {
        $shortcuts = UserShortcutMap::fromUserPreferences();
        $enabled = setting()->getForCurrentUser('ui-shortcuts-enabled', false);

        return view('users.preferences.shortcuts', [
            'shortcuts' => $shortcuts,
            'enabled' => $enabled,
        ]);
    }

    /**
     * Update the user-specific interface shortcuts.
     */
    public function updateShortcuts(Request $request)
    {
        $enabled = $request->get('enabled') === 'true';
        $providedShortcuts = $request->get('shortcut', []);
        $shortcuts = new UserShortcutMap($providedShortcuts);

        setting()->putForCurrentUser('ui-shortcuts', $shortcuts->toJson());
        setting()->putForCurrentUser('ui-shortcuts-enabled', $enabled);

        $this->showSuccessNotification(trans('preferences.shortcuts_update_success'));

        return redirect('/preferences/shortcuts');
    }

    /**
     * Show the notification preferences for the current user.
     */
    public function showNotifications(PermissionApplicator $permissions)
    {
        $this->checkPermission('receive-notifications');

        $preferences = (new UserNotificationPreferences(user()));

        $query = Watch::query()->where('user_id', '=', user()->id);
        $query = $permissions->restrictEntityRelationQuery($query, 'watches', 'watchable_id', 'watchable_type');
        $watches = $query->with('watchable')->paginate(20);

        return view('users.preferences.notifications', [
            'preferences' => $preferences,
            'watches' => $watches,
        ]);
    }

    /**
     * Update the notification preferences for the current user.
     */
    public function updateNotifications(Request $request)
    {
        $this->checkPermission('receive-notifications');
        $data = $this->validate($request, [
           'preferences' => ['required', 'array'],
           'preferences.*' => ['required', 'string'],
        ]);

        $preferences = (new UserNotificationPreferences(user()));
        $preferences->updateFromSettingsArray($data['preferences']);
        $this->showSuccessNotification(trans('preferences.notifications_update_success'));

        return redirect('/preferences/notifications');
    }

    /**
     * Update the preferred view format for a list view of the given type.
     */
    public function changeView(Request $request, string $type)
    {
        $valueViewTypes = ['books', 'bookshelves', 'bookshelf'];
        if (!in_array($type, $valueViewTypes)) {
            return redirect()->back(500);
        }

        $view = $request->get('view');
        if (!in_array($view, ['grid', 'list'])) {
            $view = 'list';
        }

        $key = $type . '_view_type';
        setting()->putForCurrentUser($key, $view);

        return redirect()->back(302, [], "/");
    }

    /**
     * Change the stored sort type for a particular view.
     */
    public function changeSort(Request $request, string $type)
    {
        $validSortTypes = ['books', 'bookshelves', 'shelf_books', 'users', 'roles', 'webhooks', 'tags', 'page_revisions'];
        if (!in_array($type, $validSortTypes)) {
            return redirect()->back(500);
        }

        $sort = substr($request->get('sort') ?: 'name', 0, 50);
        $order = $request->get('order') === 'desc' ? 'desc' : 'asc';

        $sortKey = $type . '_sort';
        $orderKey = $type . '_sort_order';
        setting()->putForCurrentUser($sortKey, $sort);
        setting()->putForCurrentUser($orderKey, $order);

        return redirect()->back(302, [], "/");
    }

    /**
     * Toggle dark mode for the current user.
     */
    public function toggleDarkMode()
    {
        $enabled = setting()->getForCurrentUser('dark-mode-enabled', false);
        setting()->putForCurrentUser('dark-mode-enabled', $enabled ? 'false' : 'true');

        return redirect()->back();
    }

    /**
     * Update the stored section expansion preference for the given user.
     */
    public function changeExpansion(Request $request, string $type)
    {
        $typeWhitelist = ['home-details'];
        if (!in_array($type, $typeWhitelist)) {
            return response('Invalid key', 500);
        }

        $newState = $request->get('expand', 'false');
        setting()->putForCurrentUser('section_expansion#' . $type, $newState);

        return response('', 204);
    }

    /**
     * Update the favorite status for a code language.
     */
    public function updateCodeLanguageFavourite(Request $request)
    {
        $validated = $this->validate($request, [
            'language' => ['required', 'string', 'max:20'],
            'active' => ['required', 'bool'],
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

        setting()->putForCurrentUser('code-language-favourites', implode(',', $currentFavorites));
        return response('', 204);
    }
}
