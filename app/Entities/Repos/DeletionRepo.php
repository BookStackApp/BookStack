<?php

namespace BookStack\Entities\Repos;

use BookStack\Actions\ActivityType;
use BookStack\Entities\Models\Deletion;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Facades\Activity;

class DeletionRepo
{
    private TrashCan $trashCan;

    public function __construct(TrashCan $trashCan)
    {
        $this->trashCan = $trashCan;
    }

    public function restore(int $id): int
    {
        /** @var Deletion $deletion */
        $deletion = Deletion::query()->findOrFail($id);
        Activity::add(ActivityType::RECYCLE_BIN_RESTORE, $deletion);

        return $this->trashCan->restoreFromDeletion($deletion);
    }

    public function destroy(int $id): int
    {
        /** @var Deletion $deletion */
        $deletion = Deletion::query()->findOrFail($id);
        Activity::add(ActivityType::RECYCLE_BIN_DESTROY, $deletion);

        return $this->trashCan->destroyFromDeletion($deletion);
    }
}
