<?php namespace BookStack\Http\Controllers;

use BookStack\Actions\ActivityType;
use BookStack\Entities\Deletion;
use BookStack\Entities\Managers\TrashCan;

class RecycleBinController extends Controller
{

    protected $recycleBinBaseUrl = '/settings/recycle-bin';

    /**
     * On each request to a method of this controller check permissions
     * using a middleware closure.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->checkPermission('settings-manage');
            $this->checkPermission('restrictions-manage-all');
            return $next($request);
        });
        parent::__construct();
    }


    /**
     * Show the top-level listing for the recycle bin.
     */
    public function index()
    {
        $deletions = Deletion::query()->with(['deletable', 'deleter'])->paginate(10);

        return view('settings.recycle-bin.index', [
            'deletions' => $deletions,
        ]);
    }

    /**
     * Show the page to confirm a restore of the deletion of the given id.
     */
    public function showRestore(string $id)
    {
        /** @var Deletion $deletion */
        $deletion = Deletion::query()->findOrFail($id);

        return view('settings.recycle-bin.restore', [
            'deletion' => $deletion,
        ]);
    }

    /**
     * Restore the element attached to the given deletion.
     * @throws \Exception
     */
    public function restore(string $id)
    {
        /** @var Deletion $deletion */
        $deletion = Deletion::query()->findOrFail($id);
        $this->logActivity(ActivityType::RECYCLE_BIN_RESTORE, $deletion);
        $restoreCount = (new TrashCan())->restoreFromDeletion($deletion);

        $this->showSuccessNotification(trans('settings.recycle_bin_restore_notification', ['count' => $restoreCount]));
        return redirect($this->recycleBinBaseUrl);
    }

    /**
     * Show the page to confirm a Permanent deletion of the element attached to the deletion of the given id.
     */
    public function showDestroy(string $id)
    {
        /** @var Deletion $deletion */
        $deletion = Deletion::query()->findOrFail($id);

        return view('settings.recycle-bin.destroy', [
            'deletion' => $deletion,
        ]);
    }

    /**
     * Permanently delete the content associated with the given deletion.
     * @throws \Exception
     */
    public function destroy(string $id)
    {
        /** @var Deletion $deletion */
        $deletion = Deletion::query()->findOrFail($id);
        $this->logActivity(ActivityType::RECYCLE_BIN_DESTROY, $deletion);
        $deleteCount = (new TrashCan())->destroyFromDeletion($deletion);

        $this->showSuccessNotification(trans('settings.recycle_bin_destroy_notification', ['count' => $deleteCount]));
        return redirect($this->recycleBinBaseUrl);
    }

    /**
     * Empty out the recycle bin.
     * @throws \Exception
     */
    public function empty()
    {
        $deleteCount = (new TrashCan())->empty();

        $this->logActivity(ActivityType::RECYCLE_BIN_EMPTY);
        $this->showSuccessNotification(trans('settings.recycle_bin_destroy_notification', ['count' => $deleteCount]));
        return redirect($this->recycleBinBaseUrl);
    }
}
