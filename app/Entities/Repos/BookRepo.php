<?php namespace BookStack\Entities\Repos;

use BookStack\Actions\TagRepo;
use BookStack\Entities\Book;
use BookStack\Entities\Managers\TrashCan;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\NotifyException;
use BookStack\Uploads\ImageRepo;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
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
     * @param $tagRepo
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
        return Book::visible()->orderBy($sort, $order)->paginate($count);
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
     * Create a new book in the system
     */
    public function create(array $input): Book
    {
        $book = new Book();
        $this->baseRepo->create($book, $input);
        return $book;
    }

    /**
     * Update the given book.
     */
    public function update(Book $book, array $input): Book
    {
        $this->baseRepo->update($book, $input);
        return $book;
    }

    /**
     * Update the given book's cover image, or clear it.
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage(Book $book, UploadedFile $coverImage = null, bool $removeImage = false)
    {
        $this->baseRepo->updateCoverImage($book, $coverImage, $removeImage);
    }

    /**
     * Update the permissions of a book.
     */
    public function updatePermissions(Book $book, bool $restricted, Collection $permissions = null)
    {
        $this->baseRepo->updatePermissions($book, $restricted, $permissions);
    }

    /**
     * Remove a book from the system.
     * @throws NotifyException
     * @throws BindingResolutionException
     */
    public function destroy(Book $book)
    {
        $trashCan = new TrashCan();
        $trashCan->destroyBook($book);
    }
}
