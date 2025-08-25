<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Queries\BookQueries;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Facades\Activity;
use BookStack\Util\DatabaseTransaction;
use Exception;

class BookshelfRepo
{
    public function __construct(
        protected BaseRepo $baseRepo,
        protected BookQueries $bookQueries,
        protected TrashCan $trashCan,
    ) {
    }

    /**
     * Create a new shelf in the system.
     */
    public function create(array $input, array $bookIds): Bookshelf
    {
        return (new DatabaseTransaction(function () use ($input, $bookIds) {
            $shelf = new Bookshelf();
            $this->baseRepo->create($shelf, $input);
            $this->baseRepo->updateCoverImage($shelf, $input['image'] ?? null);
            $this->updateBooks($shelf, $bookIds);
            Activity::add(ActivityType::BOOKSHELF_CREATE, $shelf);
            return $shelf;
        }))->run();
    }

    /**
     * Update an existing shelf in the system using the given input.
     */
    public function update(Bookshelf $shelf, array $input, ?array $bookIds): Bookshelf
    {
        $this->baseRepo->update($shelf, $input);

        if (!is_null($bookIds)) {
            $this->updateBooks($shelf, $bookIds);
        }

        if (array_key_exists('image', $input)) {
            $this->baseRepo->updateCoverImage($shelf, $input['image'], $input['image'] === null);
        }

        Activity::add(ActivityType::BOOKSHELF_UPDATE, $shelf);

        return $shelf;
    }

    /**
     * Update which books are assigned to this shelf by syncing the given book ids.
     * Function ensures the managed books are visible to the current user and existing,
     * and that the user does not alter the assignment of books that are not visible to them.
     */
    protected function updateBooks(Bookshelf $shelf, array $bookIds): void
    {
        $numericIDs = collect($bookIds)->map(function ($id) {
            return intval($id);
        });

        $existingBookIds = $shelf->books()->pluck('id')->toArray();
        $visibleExistingBookIds = $this->bookQueries->visibleForList()
            ->whereIn('id', $existingBookIds)
            ->pluck('id')
            ->toArray();
        $nonVisibleExistingBookIds = array_values(array_diff($existingBookIds, $visibleExistingBookIds));

        $newIdsToAssign = $this->bookQueries->visibleForList()
            ->whereIn('id', $bookIds)
            ->pluck('id')
            ->toArray();

        $maxNewIndex = max($numericIDs->keys()->toArray() ?: [0]);

        $syncData = [];
        foreach ($newIdsToAssign as $id) {
            $syncData[$id] = ['order' => $numericIDs->search($id)];
        }

        foreach ($nonVisibleExistingBookIds as $index => $id) {
            $syncData[$id] = ['order' => $maxNewIndex + ($index + 1)];
        }

        $shelf->books()->sync($syncData);
    }

    /**
     * Remove a bookshelf from the system.
     *
     * @throws Exception
     */
    public function destroy(Bookshelf $shelf)
    {
        $this->trashCan->softDestroyShelf($shelf);
        Activity::add(ActivityType::BOOKSHELF_DELETE, $shelf);
        $this->trashCan->autoClearOld();
    }
}
