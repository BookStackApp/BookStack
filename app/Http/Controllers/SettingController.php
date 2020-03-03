<?php namespace BookStack\Http\Controllers;

use BookStack\Auth\User;
use BookStack\Notifications\TestEmail;
use BookStack\Uploads\ImageRepo;
use BookStack\Uploads\ImageService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $imageRepo;

    /**
     * SettingController constructor.
     */
    public function __construct(ImageRepo $imageRepo)
    {
        $this->imageRepo = $imageRepo;
        parent::__construct();
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

        return view('settings.index', [
            'version' => $version,
            'guestUser' => User::getDefault()
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
            'app_logo' => $this->imageRepo->getImageValidationRules(),
        ]);

        // Cycles through posted settings and update them
        foreach ($request->all() as $name => $value) {
            if (strpos($name, 'setting-') !== 0) {
                continue;
            }
            $key = str_replace('setting-', '', trim($name));
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

        $this->showSuccessNotification(trans('settings.settings_save_success'));
        $redirectLocation = '/settings#' . $request->get('section', '');
        return redirect(rtrim($redirectLocation, '#'));
    }

    /**
     * Show the page for application maintenance.
     */
    public function showMaintenance()
    {
        $this->checkPermission('settings-manage');
        $this->setPageTitle(trans('settings.maint'));

        // Get application version
        $version = trim(file_get_contents(base_path('version')));

        return view('settings.maintenance', ['version' => $version]);
    }

    /**
     * Action to clean-up images in the system.
     */
    public function cleanupImages(Request $request, ImageService $imageService)
    {
        $this->checkPermission('settings-manage');

        $checkRevisions = !($request->get('ignore_revisions', 'false') === 'true');
        $dryRun = !($request->has('confirm'));

        $imagesToDelete = $imageService->deleteUnusedImages($checkRevisions, $dryRun);
        $deleteCount = count($imagesToDelete);
        if ($deleteCount === 0) {
            $this->showWarningNotification(trans('settings.maint_image_cleanup_nothing_found'));
            return redirect('/settings/maintenance')->withInput();
        }

        if ($dryRun) {
            session()->flash('cleanup-images-warning', trans('settings.maint_image_cleanup_warning', ['count' => $deleteCount]));
        } else {
            $this->showSuccessNotification(trans('settings.maint_image_cleanup_success', ['count' => $deleteCount]));
        }

        return redirect('/settings/maintenance#image-cleanup')->withInput();
    }

    /**
     * Action to send a test e-mail to the current user.
     */
    public function sendTestEmail()
    {
        $this->checkPermission('settings-manage');

        try {
            user()->notify(new TestEmail());
            $this->showSuccessNotification(trans('settings.maint_send_test_email_success', ['address' => user()->email]));
        } catch (\Exception $exception) {
            $errorMessage = trans('errors.maintenance_test_email_failure') . "\n" . $exception->getMessage();
            $this->showErrorNotification($errorMessage);
        }


        return redirect('/settings/maintenance#image-cleanup')->withInput();
    }
}
