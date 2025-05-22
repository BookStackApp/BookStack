<?php

namespace BookStack\Settings;

use BookStack\Activity\ActivityType;
use BookStack\App\AppVersion;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Http\Controller;
use BookStack\References\ReferenceStore;
use BookStack\Uploads\ImageService;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Show the page for application maintenance.
     */
    public function index(TrashCan $trashCan)
    {
        $this->checkPermission('settings-manage');
        $this->setPageTitle(trans('settings.maint'));

        // Recycle bin details
        $recycleStats = $trashCan->getTrashedCounts();

        return view('settings.maintenance', [
            'version'      => AppVersion::get(),
            'recycleStats' => $recycleStats,
        ]);
    }

    /**
     * Action to clean-up images in the system.
     */
    public function cleanupImages(Request $request, ImageService $imageService)
    {
        $this->checkPermission('settings-manage');
        $this->logActivity(ActivityType::MAINTENANCE_ACTION_RUN, 'cleanup-images');

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
        $this->logActivity(ActivityType::MAINTENANCE_ACTION_RUN, 'send-test-email');

        try {
            user()->notifyNow(new TestEmailNotification());
            $this->showSuccessNotification(trans('settings.maint_send_test_email_success', ['address' => user()->email]));
        } catch (\Exception $exception) {
            $errorMessage = trans('errors.maintenance_test_email_failure') . "\n" . $exception->getMessage();
            $this->showErrorNotification($errorMessage);
        }

        return redirect('/settings/maintenance#image-cleanup');
    }

    /**
     * Action to regenerate the reference index in the system.
     */
    public function regenerateReferences(ReferenceStore $referenceStore)
    {
        $this->checkPermission('settings-manage');
        $this->logActivity(ActivityType::MAINTENANCE_ACTION_RUN, 'regenerate-references');

        try {
            $referenceStore->updateForAll();
            $this->showSuccessNotification(trans('settings.maint_regen_references_success'));
        } catch (\Exception $exception) {
            $this->showErrorNotification($exception->getMessage());
        }

        return redirect('/settings/maintenance#regenerate-references');
    }
}
