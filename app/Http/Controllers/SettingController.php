<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityType;
use BookStack\Auth\User;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected ImageRepo $imageRepo;

    protected array $settingCategories = ['features', 'customization', 'registration'];

    public function __construct(ImageRepo $imageRepo)
    {
        $this->imageRepo = $imageRepo;
    }

    /**
     * Display a listing of the settings.
     */
    public function index(string $category)
    {
        $this->ensureCategoryExists($category);
        $this->checkPermission('settings-manage');
        $this->setPageTitle(trans('settings.settings'));

        // Get application version
        $version = trim(file_get_contents(base_path('version')));

        return view('settings.' . $category, [
            'category'  => $category,
            'version'   => $version,
            'guestUser' => User::getDefault(),
        ]);
    }

    /**
     * Update the specified settings in storage.
     */
    public function update(Request $request, string $category)
    {
        $this->ensureCategoryExists($category);
        $this->preventAccessInDemoMode();
        $this->checkPermission('settings-manage');
        $this->validate($request, [
            'app_logo' => array_merge(['nullable'], $this->getImageValidationRules()),
        ]);

        // Cycles through posted settings and update them
        foreach ($request->all() as $name => $value) {
            $key = str_replace('setting-', '', trim($name));
            if (strpos($name, 'setting-') !== 0) {
                continue;
            }
            setting()->put($key, $value);
        }

        // Update logo image if set
        if ($category === 'customization' && $request->hasFile('app_logo')) {
            $logoFile = $request->file('app_logo');
            $this->imageRepo->destroyByType('system');
            $image = $this->imageRepo->saveNew($logoFile, 'system', 0, null, 86);
            setting()->put('app-logo', $image->url);
        }

        // Clear logo image if requested
        if ($category === 'customization' &&  $request->get('app_logo_reset', null)) {
            $this->imageRepo->destroyByType('system');
            setting()->remove('app-logo');
        }

        $this->logActivity(ActivityType::SETTINGS_UPDATE, $category);
        $this->showSuccessNotification(trans('settings.settings_save_success'));

        return redirect("/settings/${category}");
    }

    protected function ensureCategoryExists(string $category): void
    {
        if (!in_array($category, $this->settingCategories)) {
            abort(404);
        }
    }
}
