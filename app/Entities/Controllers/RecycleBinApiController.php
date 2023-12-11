<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Deletion;
use BookStack\Entities\Repos\DeletionRepo;
use BookStack\Http\ApiController;
use Closure;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Get a top-level listing of the items in the recycle bin.
     * The "deletable" property will reflect the main item deleted.
     * For books and chapters, counts of child pages/chapters will
     * be loaded within this "deletable" data.
     * For chapters & pages, the parent item will be loaded within this "deletable" data.
     * Requires permission to manage both system settings and permissions.
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
     * Requires permission to manage both system settings and permissions.
     */
    public function restore(DeletionRepo $deletionRepo, string $deletionId)
    {
        $restoreCount = $deletionRepo->restore(intval($deletionId));

        return response()->json(['restore_count' => $restoreCount]);
    }

    /**
     * Remove a single deletion from the recycle bin.
     * Use this endpoint carefully as it will entirely remove the underlying deleted items from the system.
     * Requires permission to manage both system settings and permissions.
     */
    public function destroy(DeletionRepo $deletionRepo, string $deletionId)
    {
        $deleteCount = $deletionRepo->destroy(intval($deletionId));

        return response()->json(['delete_count' => $deleteCount]);
    }

    /**
     * Load some related details for the deletion listing.
     */
    protected function listFormatter(Deletion $deletion)
    {
        $deletable = $deletion->deletable;
        $withTrashedQuery = fn (Builder $query) => $query->withTrashed();

        if ($deletable instanceof BookChild) {
            $parent = $deletable->getParent();
            $parent->setAttribute('type', $parent->getType());
            $deletable->setRelation('parent', $parent);
        }

        if ($deletable instanceof Book || $deletable instanceof Chapter) {
            $countsToLoad = ['pages' => $withTrashedQuery];
            if ($deletable instanceof Book) {
                $countsToLoad['chapters'] = $withTrashedQuery;
            }
            $deletable->loadCount($countsToLoad);
        }
    }
}
