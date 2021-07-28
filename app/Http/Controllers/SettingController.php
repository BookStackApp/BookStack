<?php

namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityType;
use BookStack\Auth\User;
use BookStack\Exceptions\HttpFetchException;
use BookStack\Uploads\HttpFetcher;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $imageRepo;

    protected $httpFetcher;

    /**
     * SettingController constructor.
     */
    public function __construct(ImageRepo $imageRepo, HttpFetcher $httpFetcher)
    {
        $this->imageRepo = $imageRepo;
        $this->httpFetcher = $httpFetcher;
    }

    /**
     * Display a listing of the settings.
     */
    public function index()
    {
        $this->checkPermission('settings-manage');
        $this->setPageTitle(trans('settings.settings'));

        // Get application version
        $version = trim(file_get_contents(base_path('version')));

        // Get latest release version from Github
        $isLatestVersion = $this->checkLatestVersion($version);

        return view('settings.index', [
            'version' => $version,
            'isLatestVersion' => $isLatestVersion,
            'guestUser' => User::getDefault(),
        ]);
    }

    /**
     * Update the specified settings in storage.
     */
    public function update(Request $request)
    {
        $this->preventAccessInDemoMode();
        $this->checkPermission('settings-manage');
        $this->validate($request, [
            'app_logo' => 'nullable|' . $this->getImageValidationRules(),
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
        if ($request->hasFile('app_logo')) {
            $logoFile = $request->file('app_logo');
            $this->imageRepo->destroyByType('system');
            $image = $this->imageRepo->saveNew($logoFile, 'system', 0, null, 86);
            setting()->put('app-logo', $image->url);
        }

        // Clear logo image if requested
        if ($request->get('app_logo_reset', null)) {
            $this->imageRepo->destroyByType('system');
            setting()->remove('app-logo');
        }

        $section = $request->get('section', '');
        $this->logActivity(ActivityType::SETTINGS_UPDATE, $section);
        $this->showSuccessNotification(trans('settings.settings_save_success'));
        $redirectLocation = '/settings#' . $section;

        return redirect(rtrim($redirectLocation, '#'));
    }

    private function checkLatestVersion($version)
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        return cache()->remember('github_is_latest_release_version', 7200, function () use ($version) {
            try {
                $releaseInfo = $this->httpFetcher->fetch('https://api.github.com/repos/BookStackApp/BookStack/releases/latest');
                if (!$releaseInfo) {
                    return true;
                }

                $decoded = json_decode($releaseInfo);

                if ($decoded === null) {
                    return true;
                }
                $latestVersion = $decoded->tag_name;

                if (!$latestVersion) {
                    return true;
                }

                return version_compare(
                    str_replace('v', '', $version),
                    str_replace('v', '', $latestVersion),
                    '>');

            } catch (HttpFetchException $e) {
                return true;
            }
        });
    }
}
