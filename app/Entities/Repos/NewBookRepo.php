<?php namespace BookStack\Entities\Repos;

use BookStack\Actions\TagRepo;
use BookStack\Entities\Book;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Uploads\ImageRepo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class NewBookRepo
{

    protected $tagRepo;
    protected $imageRepo;

    /**
     * NewBookRepo constructor.
     * @param $tagRepo
     */
    public function __construct(TagRepo $tagRepo, ImageRepo $imageRepo)
    {
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
            ->orderBy('last_viewed_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most popular books in the system.
     */
    public function getPopular(int $count = 20): Collection
    {
        return Book::visible()->withViewCount()
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
        return Book::visible()->where('slug', '=', $slug)->firstOrFail();
    }

    /**
     * Create a new book in the system
     * @throws ImageUploadException
     */
    public function create(array $input, UploadedFile $coverImage = null): Book
    {
        $book = new Book($input);
        $book->forceFill([
            'created_by' => user()->id,
            'updated_by' => user()->id,
        ]);
        $book->refreshSlug();
        $book->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($book, $input['tags']);
        }

        $book->rebuildPermissions();
        $book->indexForSearch();

        if ($coverImage) {
            $image = $this->imageRepo->saveNew($coverImage, 'cover_book', $book->id, 512, 512, true);
            $book->cover()->associate($image);
        }

        return $book;
    }

}