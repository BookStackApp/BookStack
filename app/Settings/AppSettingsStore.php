<?php

namespace BookStack\Settings;

use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;

class AppSettingsStore
{
    protected ImageRepo $imageRepo;

    public function __construct(ImageRepo $imageRepo)
    {
        $this->imageRepo = $imageRepo;
    }

    public function storeFromUpdateRequest(Request $request, string $category)
    {
        $this->storeSimpleSettings($request);
        if ($category === 'customization') {
            $this->updateAppLogo($request);
            $this->updateAppIcon($request);
        }
    }

    protected function updateAppIcon(Request $request): void
    {
        $sizes = [128, 64, 32];

        // Update icon image if set
        if ($request->hasFile('app_icon')) {
            $iconFile = $request->file('app_icon');
            $this->destroyExistingSettingImage('app-icon');
            $image = $this->imageRepo->saveNew($iconFile, 'system', 0, 256, 256);
            setting()->put('app-icon', $image->url);

            foreach ($sizes as $size) {
                $icon = $this->imageRepo->saveNew($iconFile, 'system', 0, $size, $size);
                setting()->put('app-icon-' . $size, $icon->url);
            }
        }

        // Clear icon image if requested
        if ($request->get('app_icon_reset')) {
            $this->destroyExistingSettingImage('app-icon');
            setting()->remove('app-icon');
            foreach ($sizes as $size) {
                $this->destroyExistingSettingImage('app-icon-' . $size);
                setting()->remove('app-icon-' . $size);
            }
        }
    }

    protected function updateAppLogo(Request $request): void
    {
        // Update logo image if set
        if ($request->hasFile('app_logo')) {
            $logoFile = $request->file('app_logo');
            $this->destroyExistingSettingImage('app-logo');
            $image = $this->imageRepo->saveNew($logoFile, 'system', 0, null, 86);
            setting()->put('app-logo', $image->url);
        }

        // Clear logo image if requested
        if ($request->get('app_logo_reset')) {
            $this->destroyExistingSettingImage('app-logo');
            setting()->remove('app-logo');
        }
    }

    protected function storeSimpleSettings(Request $request): void
    {
        foreach ($request->all() as $name => $value) {
            if (strpos($name, 'setting-') !== 0) {
                continue;
            }

            $key = str_replace('setting-', '', trim($name));
            setting()->put($key, $value);
        }
    }

    protected function destroyExistingSettingImage(string $settingKey)
    {
        $existingVal = setting()->get($settingKey);
        if ($existingVal) {
            $this->imageRepo->destroyByUrlAndType($existingVal, 'system');
        }
    }
}
