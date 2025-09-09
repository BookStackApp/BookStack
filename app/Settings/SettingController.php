<?php

namespace BookStack\Settings;

use BookStack\Activity\ActivityType;
use BookStack\App\AppVersion;
use BookStack\Http\Controller;
use BookStack\Users\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Handle requests to the settings index path.
     */
    public function index()
    {
        return redirect('/settings/features');
    }

    /**
     * Display the settings for the given category.
     */
    public function category(string $category)
    {
        $this->ensureCategoryExists($category);
        $this->checkPermission('settings-manage');
        $this->setPageTitle(trans('settings.settings'));

        return view('settings.categories.' . $category, [
            'category'  => $category,
            'version'   => AppVersion::get(),
            'guestUser' => User::getGuest(),
        ]);
    }

    /**
     * Update the specified settings in storage.
     */
    public function update(Request $request, AppSettingsStore $store, string $category)
    {
        $this->ensureCategoryExists($category);
        $this->preventAccessInDemoMode();
        $this->checkPermission('settings-manage');
        $this->validate($request, [
            'app_logo' => ['nullable', ...$this->getImageValidationRules()],
            'app_icon' => ['nullable', ...$this->getImageValidationRules()],
        ]);

        $store->storeFromUpdateRequest($request, $category);
        $this->logActivity(ActivityType::SETTINGS_UPDATE, $category);

        return redirect("/settings/{$category}");
    }

    protected function ensureCategoryExists(string $category): void
    {
        if (!view()->exists('settings.categories.' . $category)) {
            abort(404);
        }
    }
}
