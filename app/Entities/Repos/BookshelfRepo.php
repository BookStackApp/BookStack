<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Facades\Activity;
use Exception;

class BookshelfRepo
{
    public function __construct(
        protected BaseRepo $baseRepo,
    ) {
    }

    /**
     * Create a new shelf in the system.
     */
    public function create(array $input, array $bookIds): Bookshelf
    {
        $shelf = new Bookshelf();
        $this->baseRepo->create($shelf, $input);
        $this->baseRepo->updateCoverImage($shelf, $input['image'] ?? null);
        $this->updateBooks($shelf, $bookIds);
        Activity::add(ActivityType::BOOKSHELF_CREATE, $shelf);

        return $shelf;
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
     * Function ensures the books are visible to the current user and existing.
     */
    protected function updateBooks(Bookshelf $shelf, array $bookIds)
    {
        $numericIDs = collect($bookIds)->map(function ($id) {
            return intval($id);
        });

        $syncData = Book::visible()
            ->whereIn('id', $bookIds)
            ->pluck('id')
            ->mapWithKeys(function ($bookId) use ($numericIDs) {
                return [$bookId => ['order' => $numericIDs->search($bookId)]];
            });

        $shelf->books()->sync($syncData);
    }

    /**
     * Remove a bookshelf from the system.
     *
     * @throws Exception
     */
    public function destroy(Bookshelf $shelf)
    {
        $trashCan = new TrashCan();
        $trashCan->softDestroyShelf($shelf);
        Activity::add(ActivityType::BOOKSHELF_DELETE, $shelf);
        $trashCan->autoClearOld();
    }
}
