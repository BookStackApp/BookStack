<?php namespace BookStack\Entities\Repos;

use BookStack\Actions\TagRepo;
use BookStack\Entities\Book;
use BookStack\Entities\Managers\TrashCan;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotifyException;
use BookStack\Uploads\ImageRepo;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class BookRepo
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
     */
    public function create(array $input): Book
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

        return $book;
    }

    /**
     * Update the given book.
     */
    public function update(Book $book, array $input): Book
    {
        $book->fill($input);
        $book->updated_by = user()->id;


        if ($book->isDirty('name')) {
            $book->refreshSlug();
        }

        $book->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($book, $input['tags']);
        }

        $book->rebuildPermissions();
        $book->indexForSearch();

        return $book;
    }

    /**
     * Update the given book's cover image, or clear it.
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage(Book $book, UploadedFile $coverImage = null, bool $removeImage = false)
    {
        if ($coverImage) {
            $this->imageRepo->destroyImage($book->cover);
            $image = $this->imageRepo->saveNew($coverImage, 'cover_book', $book->id, 512, 512, true);
            $book->cover()->associate($image);
        }

        if ($removeImage) {
            $this->imageRepo->destroyImage($book->cover);
            $book->image_id = 0;
            $book->save();
        }
    }

    /**
     * Update the permissions of a book.
     */
    public function updatePermissions(Book $book, bool $restricted, Collection $permissions = null)
    {
        $book->restricted = $restricted;
        $book->permissions()->delete();

        if (!is_null($permissions)) {
            $entityPermissionData = $permissions->flatMap(function($restrictions, $roleId) {
                return collect($restrictions)->keys()->map(function($action) use ($roleId) {
                    return [
                        'role_id' => $roleId,
                        'action' => strtolower($action),
                    ] ;
                });
            });

            $book->permissions()->createMany($entityPermissionData);
        }

        $book->save();
        $book->rebuildPermissions();
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