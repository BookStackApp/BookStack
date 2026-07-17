<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Activity\TagRepo;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Facades\Activity;
use BookStack\Sorting\SortRule;
use BookStack\Uploads\ImageRepo;
use BookStack\Util\DatabaseTransaction;
use Exception;
use Illuminate\Http\UploadedFile;

class BookRepo
{
    public function __construct(
        protected BaseRepo $baseRepo,
        protected TagRepo $tagRepo,
        protected ImageRepo $imageRepo,
        protected TrashCan $trashCan,
    ) {
    }

    /**
     * Create a new book in the system.
     */
    public function create(array $input): Book
    {
        return (new DatabaseTransaction(function () use ($input) {
            $book = $this->baseRepo->create(new Book(), $input);
            $this->baseRepo->updateCoverImage($book, $input['image'] ?? null);
            $book->defaultTemplate()->setFromId(intval($input['default_template_id'] ?? null));
            Activity::add(ActivityType::BOOK_CREATE, $book);

            $defaultBookSortSetting = intval(setting('sorting-book-default', '0'));
            if ($defaultBookSortSetting && SortRule::query()->find($defaultBookSortSetting)) {
                $book->sort_rule_id = $defaultBookSortSetting;
            }

            $book->save();

            return $book;
        }))->run();
    }

    /**
     * Update the given book.
     */
    public function update(Book $book, array $input): Book
    {
        $book = $this->baseRepo->update($book, $input);

        if (array_key_exists('default_template_id', $input)) {
            $book->defaultTemplate()->setFromId(intval($input['default_template_id']));
        }

        if (array_key_exists('image', $input)) {
            $this->baseRepo->updateCoverImage($book, $input['image'], $input['image'] === null);
        }

        $book->save();
        Activity::add(ActivityType::BOOK_UPDATE, $book);

        return $book;
    }

    /**
     * Update the given book's cover image or clear it.
     *
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage(Book $book, ?UploadedFile $coverImage, bool $removeImage = false): void
    {
        $this->baseRepo->updateCoverImage($book, $coverImage, $removeImage);
    }

    /**
     * Remove a book from the system.
     *
     * @throws Exception
     */
    public function destroy(Book $book): void
    {
        $this->trashCan->softDestroyBook($book);
        Activity::add(ActivityType::BOOK_DELETE, $book);

        $this->trashCan->autoClearOld();
    }
}
