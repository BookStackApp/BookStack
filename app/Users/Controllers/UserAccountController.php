<?php

namespace BookStack\Users\Controllers;

use BookStack\Http\Controller;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Settings\UserNotificationPreferences;
use BookStack\Settings\UserShortcutMap;
use BookStack\Users\UserRepo;
use Illuminate\Http\Request;

class UserAccountController extends Controller
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
        $guest = user()->isGuest();
        $mfaMethods = $guest ? [] : user()->mfaValues->groupBy('method');

        return view('users.account.index', [
            'mfaMethods' => $mfaMethods,
        ]);
    }

    /**
     * Show the user-specific interface shortcuts.
     */
    public function showShortcuts()
    {
        $shortcuts = UserShortcutMap::fromUserPreferences();
        $enabled = setting()->getForCurrentUser('ui-shortcuts-enabled', false);

        $this->setPageTitle(trans('preferences.shortcuts_interface'));

        return view('users.account.shortcuts', [
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

        return redirect('/my-account/shortcuts');
    }

    /**
     * Show the notification preferences for the current user.
     */
    public function showNotifications(PermissionApplicator $permissions)
    {
        $this->checkPermission('receive-notifications');
        $this->preventGuestAccess();

        $preferences = (new UserNotificationPreferences(user()));

        $query = user()->watches()->getQuery();
        $query = $permissions->restrictEntityRelationQuery($query, 'watches', 'watchable_id', 'watchable_type');
        $query = $permissions->filterDeletedFromEntityRelationQuery($query, 'watches', 'watchable_id', 'watchable_type');
        $watches = $query->with('watchable')->paginate(20);

        $this->setPageTitle(trans('preferences.notifications'));
        return view('users.account.notifications', [
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
        $this->preventGuestAccess();
        $data = $this->validate($request, [
           'preferences' => ['required', 'array'],
           'preferences.*' => ['required', 'string'],
        ]);

        $preferences = (new UserNotificationPreferences(user()));
        $preferences->updateFromSettingsArray($data['preferences']);
        $this->showSuccessNotification(trans('preferences.notifications_update_success'));

        return redirect('/my-account/notifications');
    }
}
