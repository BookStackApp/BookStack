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
            $book = new Book();

            $this->baseRepo->create($book, $input);
            $this->baseRepo->updateCoverImage($book, $input['image'] ?? null);
            $this->baseRepo->updateDefaultTemplate($book, intval($input['default_template_id'] ?? null));
            Activity::add(ActivityType::BOOK_CREATE, $book);

            $defaultBookSortSetting = intval(setting('sorting-book-default', '0'));
            if ($defaultBookSortSetting && SortRule::query()->find($defaultBookSortSetting)) {
                $book->sort_rule_id = $defaultBookSortSetting;
                $book->save();
            }

            return $book;
        }))->run();
    }

    /**
     * Update the given book.
     */
    public function update(Book $book, array $input): Book
    {
        $this->baseRepo->update($book, $input);

        if (array_key_exists('default_template_id', $input)) {
            $this->baseRepo->updateDefaultTemplate($book, intval($input['default_template_id']));
        }

        if (array_key_exists('image', $input)) {
            $this->baseRepo->updateCoverImage($book, $input['image'], $input['image'] === null);
        }

        Activity::add(ActivityType::BOOK_UPDATE, $book);

        return $book;
    }

    /**
     * Update the given book's cover image, or clear it.
     *
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage(Book $book, ?UploadedFile $coverImage, bool $removeImage = false)
    {
        $this->baseRepo->updateCoverImage($book, $coverImage, $removeImage);
    }

    /**
     * Remove a book from the system.
     *
     * @throws Exception
     */
    public function destroy(Book $book)
    {
        $this->trashCan->softDestroyBook($book);
        Activity::add(ActivityType::BOOK_DELETE, $book);

        $this->trashCan->autoClearOld();
    }
}
