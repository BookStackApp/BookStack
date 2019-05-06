<?php namespace BookStack\Http\Controllers;

use BookStack\Auth\User;
use BookStack\Uploads\ImageRepo;
use BookStack\Uploads\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Setting;

class SettingController extends Controller
{
    protected $imageRepo;

    /**
     * SettingController constructor.
     * @param $imageRepo
     */
    public function __construct(ImageRepo $imageRepo)
    {
        $this->imageRepo = $imageRepo;
        parent::__construct();
    }


    /**
     * Display a listing of the settings.
     * @return Response
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
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $this->preventAccessForDemoUsers();
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
        if ($request->has('app_logo')) {
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

        session()->flash('success', trans('settings.settings_save_success'));
        return redirect('/settings');
    }

    /**
     * Show the page for application maintenance.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @param Request $request
     * @param ImageService $imageService
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function cleanupImages(Request $request, ImageService $imageService)
    {
        $this->checkPermission('settings-manage');

        $checkRevisions = !($request->get('ignore_revisions', 'false') === 'true');
        $dryRun = !($request->has('confirm'));

        $imagesToDelete = $imageService->deleteUnusedImages($checkRevisions, $dryRun);
        $deleteCount = count($imagesToDelete);
        if ($deleteCount === 0) {
            session()->flash('warning', trans('settings.maint_image_cleanup_nothing_found'));
            return redirect('/settings/maintenance')->withInput();
        }

        if ($dryRun) {
            session()->flash('cleanup-images-warning', trans('settings.maint_image_cleanup_warning', ['count' => $deleteCount]));
        } else {
            session()->flash('success', trans('settings.maint_image_cleanup_success', ['count' => $deleteCount]));
        }

        return redirect('/settings/maintenance#image-cleanup')->withInput();
    }
}
