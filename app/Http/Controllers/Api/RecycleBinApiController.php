<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Deletion;
use BookStack\Entities\Repos\DeletionRepo;
use Closure;

class RecycleBinApiController extends ApiController
{
    protected $fieldsToExpose = [
        'id', 'deleted_by', 'created_at', 'updated_at', 'deletable_type', 'deletable_id',
    ];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->checkPermission('settings-manage');
            $this->checkPermission('restrictions-manage-all');

            return $next($request);
        });
    }

    /**
     * Get a top-level listing of the items in the recycle bin.
     * Requires the permission to manage settings and restrictions.
     */
    public function list()
    {
        return $this->apiListingResponse(Deletion::query()->with('deletable'), [
            'id',
            'deleted_by',
            'created_at',
            'updated_at',
            'deletable_type',
            'deletable_id',
        ], [Closure::fromCallable([$this, 'listFormatter'])]);
    }

    /**
     * Restore a single deletion from the recycle bin.
     * You must provide the deletion id, not the id of the corresponding deleted item.
     */
    public function restore(DeletionRepo $deletionRepo, string $id)
    {
        $restoreCount = $deletionRepo->restore((int) $id);

        return response()->json(['restore_count' => $restoreCount]);
    }

    /**
     * Remove a single deletion from the recycle bin.
     * Use this endpoint carefully as it will entirely remove the underlying deleted items from the system.
     * You must provide the deletion id, not the id of the corresponding deleted item.
     */
    public function destroy(DeletionRepo $deletionRepo, string $id)
    {
        $deleteCount = $deletionRepo->destroy((int) $id);

        return response()->json(['delete_count' => $deleteCount]);
    }

    protected function listFormatter(Deletion $deletion)
    {
        $deletion->makeVisible($this->fieldsToExpose);
        $deletion->makeHidden('deletable');

        $deletable = $deletion->deletable;
        $isBook = $deletion->deletable_type === "BookStack\Book";
        $parent = null;
        $children = null;

        if ($isBook) {
            $chapterCount = $deletable->chapters()->withTrashed()->count();
            $children['BookStack\Chapter'] = $chapterCount;
        }

        if ($isBook || $deletion->deletable_type === "BookStack\Chapter") {
            $pageCount = $deletable->pages()->withTrashed()->count();
            $children['BookStack\Page'] = $pageCount;
        }

        $parentEntity = $deletable->getParent();
        $parent = null;

        if ($parentEntity) {
            $parent['type'] = $parentEntity->getMorphClass();
            $parent['id'] = $parentEntity->getKey();
        }

        $deletion->setAttribute('parent', $parent);
        $deletion->setAttribute('children', $children);
    }
}
