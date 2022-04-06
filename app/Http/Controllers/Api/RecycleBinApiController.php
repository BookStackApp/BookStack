<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Actions\ActivityType;
use BookStack\Entities\Models\Deletion;
use BookStack\Entities\Repos\DeletionRepo;
use BookStack\Entities\Tools\TrashCan;

class RecycleBinApiController extends ApiController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->checkPermission('settings-manage');
            $this->checkPermission('restrictions-manage-all');

            return $next($request);
        });
    }

    public function list()
    {
        return $this->apiListingResponse(Deletion::query(), [
            'id', 
            'deleted_by', 
            'created_at',
            'updated_at',
            'deletable_type',
            'deletable_id'
        ]);
    }

    public function restore(DeletionRepo $deletionRepo, string $id)
    {
        $restoreCount = $deletionRepo->restore((int) $id);
        return response()->json(['restore_count' => $restoreCount]);
    }

    public function destroy(DeletionRepo $deletionRepo, string $id)
    {
        $deleteCount = $deletionRepo->destroy((int) $id);
        return response()->json(['delete_count' => $deleteCount]);
    }
}