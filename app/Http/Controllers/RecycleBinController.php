<?php namespace BookStack\Http\Controllers;

use BookStack\Entities\Deletion;
use BookStack\Entities\Managers\TrashCan;
use Illuminate\Http\Request;

class RecycleBinController extends Controller
{
    /**
     * Show the top-level listing for the recycle bin.
     */
    public function index()
    {
        $this->checkPermission('settings-manage');
        $this->checkPermission('restrictions-manage-all');

        $deletions = Deletion::query()->with(['deletable', 'deleter'])->paginate(10);

        return view('settings.recycle-bin', [
            'deletions' => $deletions,
        ]);
    }

    /**
     * Empty out the recycle bin.
     */
    public function empty()
    {
        $this->checkPermission('settings-manage');
        $this->checkPermission('restrictions-manage-all');

        (new TrashCan())->destroyFromAllDeletions();
        return redirect('/settings/recycle-bin');
    }
}
