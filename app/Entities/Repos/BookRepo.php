<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\ActivityType;
use BookStack\Activity\TagRepo;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Tools\TrashCan;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Facades\Activity;
use BookStack\Uploads\ImageRepo;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class BookRepo
{
    protected $baseRepo;
    protected $tagRepo;
    protected $imageRepo;

    /**
     * BookRepo constructor.
     */
    public function __construct(BaseRepo $baseRepo, TagRepo $tagRepo, ImageRepo $imageRepo)
    {
        $this->baseRepo = $baseRepo;
        $this->tagRepo = $tagRepo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Get all books in a paginated format.
     */
    public function getAllPaginated(int $count = 20, string $sort = 'name', string $order = 'asc'): LengthAwarePaginator
    {
        return Book::visible()->with('cover')->orderBy($sort, $order)->paginate($count);
    }

    /**
     * Get the books that were most recently viewed by this user.
     */
    public function getRecentlyViewed(int $count = 20): Collection
    {
        return Book::visible()->withLastView()
            ->having('last_viewed_at', '>', 0)
            ->orderBy('last_viewed_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most popular books in the system.
     */
    public function getPopular(int $count = 20): Collection
    {
        return Book::visible()->withViewCount()
            ->having('view_count', '>', 0)
            ->orderBy('view_count', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most recently created books from the system.
     */
    public function getRecentlyCreated(int $count = 20): Collection
    {
        return Book::visible()->orderBy('created_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get a book by its slug.
     */
    public function getBySlug(string $slug): Book
    {
        $book = Book::visible()->where('slug', '=', $slug)->first();

        if ($book === null) {
            throw new NotFoundException(trans('errors.book_not_found'));
        }

        return $book;
    }

    /**
     * Create a new book in the system.
     */
    public function create(array $input): Book
    {
        $book = new Book();
        $this->baseRepo->create($book, $input);
        $this->baseRepo->updateCoverImage($book, $input['image'] ?? null);
        Activity::add(ActivityType::BOOK_CREATE, $book);

        return $book;
    }

    /**
     * Update the given book.
     */
    public function update(Book $book, array $input): Book
    {
        $this->baseRepo->update($book, $input);

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
        $trashCan = new TrashCan();
        $trashCan->softDestroyBook($book);
        Activity::add(ActivityType::BOOK_DELETE, $book);

        $trashCan->autoClearOld();
    }
}
