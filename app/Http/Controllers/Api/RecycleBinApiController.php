<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Deletion;
use BookStack\Entities\Repos\DeletionRepo;
use Closure;

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
            'deletable_id',
        ], [Closure::fromCallable([$this, 'listFormatter'])]);
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

    protected function listFormatter(Deletion $deletion)
    {
        $deletable = $deletion->deletable;
        $isBook = $deletable instanceof Book;
        $parent = null;
        $children = null;

        if ($isBook) {
            $chapterCount = $deletable->chapters()->withTrashed()->count();       
            $children['Bookstack\Chapter'] = $chapterCount;
        }

        if ($isBook || $deletion->deletable instanceof Chapter) {
            $pageCount = $deletable->pages()->withTrashed()->count();     
            $children['Bookstack\Page'] = $pageCount;
        }

        $parentEntity = $deletable->getParent();
        $parent = [];

        if ($parentEntity) {
            $parent['type'] = $parentEntity->getMorphClass();
            $parent['id'] = $parentEntity->getKey();
        }

        $deletion->setAttribute('parent', $parent);
        $deletion->setAttribute('children', $children);
    }
}
