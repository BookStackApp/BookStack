<?php


namespace BookStack\Entities\Repos;


use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Managers\TrashCan;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Exceptions\NotifyException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class BookshelfRepo
{
    protected $baseRepo;

    /**
     * BookshelfRepo constructor.
     * @param $baseRepo
     */
    public function __construct(BaseRepo $baseRepo)
    {
        $this->baseRepo = $baseRepo;
    }


    /**
     * Get all bookshelves in a paginated format.
     */
    public function getAllPaginated(int $count = 20, string $sort = 'name', string $order = 'asc'): LengthAwarePaginator
    {
        return Bookshelf::visible()->with('visibleBooks')->orderBy($sort, $order)->paginate($count);
    }

    /**
     * Get the bookshelves that were most recently viewed by this user.
     */
    public function getRecentlyViewed(int $count = 20): Collection
    {
        return Bookshelf::visible()->withLastView()
            ->orderBy('last_viewed_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most popular bookshelves in the system.
     */
    public function getPopular(int $count = 20): Collection
    {
        return Bookshelf::visible()->withViewCount()
            ->orderBy('view_count', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most recently created bookshelves from the system.
     */
    public function getRecentlyCreated(int $count = 20): Collection
    {
        return Bookshelf::visible()->orderBy('created_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get a shelf by its slug.
     */
    public function getBySlug(string $slug): Bookshelf
    {
        return Bookshelf::visible()->where('slug', '=', $slug)->firstOrFail();
    }

    /**
     * Create a new shelf in the system.
     */
    public function create(array $input, array $bookIds): Bookshelf
    {
        $shelf = new Bookshelf();
        $this->baseRepo->create($shelf, $input);
        $this->updateBooks($shelf, $bookIds);
        return $shelf;
    }

    /**
     * Create a new shelf in the system.
     */
    public function update(Bookshelf $shelf, array $input, array $bookIds): Bookshelf
    {
        $this->baseRepo->update($shelf, $input);
        $this->updateBooks($shelf, $bookIds);
        return $shelf;
    }

    /**
     * Update which books are assigned to this shelf by
     * syncing the given book ids.
     * Function ensures the books are visible to the current user and existing.
     */
    protected function updateBooks(Bookshelf $shelf, array $bookIds)
    {
        $numericIDs = collect($bookIds)->map(function($id) {
            return intval($id);
        });

        $syncData = Book::visible()
            ->whereIn('id', $bookIds)
            ->get(['id'])->pluck('id')->mapWithKeys(function($bookId) use ($numericIDs) {
                return [$bookId => ['order' => $numericIDs->search($bookId)]];
            });

        $shelf->books()->sync($syncData);
    }

    /**
     * Update the given shelf cover image, or clear it.
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage(Bookshelf $shelf, UploadedFile $coverImage = null, bool $removeImage = false)
    {
        $this->baseRepo->updateCoverImage($shelf, $coverImage, $removeImage);
    }

    /**
     * Update the permissions of a bookshelf.
     */
    public function updatePermissions(Bookshelf $shelf, bool $restricted, Collection $permissions = null)
    {
        $this->baseRepo->updatePermissions($shelf, $restricted, $permissions);
    }

    /**
     * Copy down the permissions of the given shelf to all child books.
     */
    public function copyDownPermissions(Bookshelf $shelf): int
    {
        $shelfPermissions = $shelf->permissions()->get(['role_id', 'action'])->toArray();
        $shelfBooks = $shelf->books()->get();
        $updatedBookCount = 0;

        /** @var Book $book */
        foreach ($shelfBooks as $book) {
            if (!userCan('restrictions-manage', $book)) {
                continue;
            }
            $book->permissions()->delete();
            $book->restricted = $shelf->restricted;
            $book->permissions()->createMany($shelfPermissions);
            $book->save();
            $book->rebuildPermissions();
            $updatedBookCount++;
        }

        return $updatedBookCount;
    }

    /**
     * Remove a bookshelf from the system.
     * @throws Exception
     */
    public function destroy(Bookshelf $shelf)
    {
        $trashCan = new TrashCan();
        $trashCan->destroyShelf($shelf);
    }

}