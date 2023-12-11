<?php

namespace BookStack\Settings;

use BookStack\Activity\ActivityType;
use BookStack\Http\Controller;
use BookStack\Users\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected array $settingCategories = ['features', 'customization', 'registration'];

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

        // Get application version
        $version = trim(file_get_contents(base_path('version')));

        return view('settings.' . $category, [
            'category'  => $category,
            'version'   => $version,
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
        if (!in_array($category, $this->settingCategories)) {
            abort(404);
        }
    }
}
