<?php

namespace BookStack\Users\Controllers;

use BookStack\Access\SocialDriverManager;
use BookStack\Http\Controller;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Settings\UserNotificationPreferences;
use BookStack\Settings\UserShortcutMap;
use BookStack\Uploads\ImageRepo;
use BookStack\Users\UserRepo;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserAccountController extends Controller
{
    public function __construct(
        protected UserRepo $userRepo,
    ) {
        $this->middleware(function (Request $request, Closure $next) {
            $this->preventGuestAccess();
            return $next($request);
        });
    }

    /**
     * Redirect the root my-account path to the main/first category.
     * Required as a controller method, instead of the Route::redirect helper,
     * to ensure the URL is generated correctly.
     */
    public function redirect()
    {
        return redirect('/my-account/profile');
    }

    /**
     * Show the profile form interface.
     */
    public function showProfile()
    {
        $this->setPageTitle(trans('preferences.profile'));

        return view('users.account.profile', [
            'model' => user(),
            'category' => 'profile',
        ]);
    }

    /**
     * Handle the submission of the user profile form.
     */
    public function updateProfile(Request $request, ImageRepo $imageRepo)
    {
        $this->preventAccessInDemoMode();

        $user = user();
        $validated = $this->validate($request, [
            'name'             => ['min:2', 'max:100'],
            'email'            => ['min:2', 'email', 'unique:users,email,' . $user->id],
            'language'         => ['string', 'max:15', 'alpha_dash'],
            'profile_image'    => array_merge(['nullable'], $this->getImageValidationRules()),
        ]);

        $this->userRepo->update($user, $validated, userCan('users-manage'));

        // Save profile image if in request
        if ($request->hasFile('profile_image')) {
            $imageUpload = $request->file('profile_image');
            $imageRepo->destroyImage($user->avatar);
            $image = $imageRepo->saveNew($imageUpload, 'user', $user->id);
            $user->image_id = $image->id;
            $user->save();
        }

        // Delete the profile image if reset option is in request
        if ($request->has('profile_image_reset')) {
            $imageRepo->destroyImage($user->avatar);
            $user->image_id = 0;
            $user->save();
        }

        return redirect('/my-account/profile');
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
            'category' => 'shortcuts',
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

        $preferences = (new UserNotificationPreferences(user()));

        $query = user()->watches()->getQuery();
        $query = $permissions->restrictEntityRelationQuery($query, 'watches', 'watchable_id', 'watchable_type');
        $query = $permissions->filterDeletedFromEntityRelationQuery($query, 'watches', 'watchable_id', 'watchable_type');
        $watches = $query->with('watchable')->paginate(20);

        $this->setPageTitle(trans('preferences.notifications'));
        return view('users.account.notifications', [
            'category' => 'notifications',
            'preferences' => $preferences,
            'watches' => $watches,
        ]);
    }

    /**
     * Update the notification preferences for the current user.
     */
    public function updateNotifications(Request $request)
    {
        $this->preventAccessInDemoMode();
        $this->checkPermission('receive-notifications');
        $data = $this->validate($request, [
           'preferences' => ['required', 'array'],
           'preferences.*' => ['required', 'string'],
        ]);

        $preferences = (new UserNotificationPreferences(user()));
        $preferences->updateFromSettingsArray($data['preferences']);
        $this->showSuccessNotification(trans('preferences.notifications_update_success'));

        return redirect('/my-account/notifications');
    }

    /**
     * Show the view for the "Access & Security" account options.
     */
    public function showAuth(SocialDriverManager $socialDriverManager)
    {
        $mfaMethods = user()->mfaValues()->get()->groupBy('method');

        $this->setPageTitle(trans('preferences.auth'));

        return view('users.account.auth', [
            'category' => 'auth',
            'mfaMethods' => $mfaMethods,
            'authMethod' => config('auth.method'),
            'activeSocialDrivers' => $socialDriverManager->getActive(),
        ]);
    }

    /**
     * Handle the submission for the auth change password form.
     */
    public function updatePassword(Request $request)
    {
        $this->preventAccessInDemoMode();

        if (config('auth.method') !== 'standard') {
            $this->showPermissionError();
        }

        $validated = $this->validate($request, [
            'password'         => ['required_with:password_confirm', Password::default()],
            'password-confirm' => ['same:password', 'required_with:password'],
        ]);

        $this->userRepo->update(user(), $validated, false);

        $this->showSuccessNotification(trans('preferences.auth_change_password_success'));

        return redirect('/my-account/auth');
    }

    /**
     * Show the user self-delete page.
     */
    public function delete()
    {
        $this->setPageTitle(trans('preferences.delete_my_account'));

        return view('users.account.delete', [
            'category' => 'profile',
        ]);
    }

    /**
     * Remove the current user from the system.
     */
    public function destroy(Request $request)
    {
        $this->preventAccessInDemoMode();

        $requestNewOwnerId = intval($request->get('new_owner_id')) ?: null;
        $newOwnerId = userCan('users-manage') ? $requestNewOwnerId : null;

        $this->userRepo->destroy(user(), $newOwnerId);

        return redirect('/');
    }
}
