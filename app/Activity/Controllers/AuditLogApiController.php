<?php

namespace BookStack\Activity\Controllers;

use BookStack\Activity\Models\Activity;
use BookStack\Http\ApiController;

class AuditLogApiController extends ApiController
{
    /**
     * Get a listing of audit log events in the system.
     * The loggable relation fields currently only relates to core
     * content types (page, book, bookshelf, chapter) but this may be
     * used more in the future across other types.
     * Requires permission to manage both users and system settings.
     */
    public function list()
    {
        $this->checkPermission('settings-manage');
        $this->checkPermission('users-manage');

        $query = Activity::query()->with(['user']);

        return $this->apiListingResponse($query, [
            'id', 'type', 'detail', 'user_id', 'loggable_id', 'loggable_type', 'ip', 'created_at',
        ]);
    }
}
