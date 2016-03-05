<?php namespace BookStack\Repos;


use Activity;
use BookStack\Exceptions\NotFoundException;
use BookStack\Services\RestrictionService;
use Illuminate\Support\Str;
use BookStack\Chapter;

class ChapterRepo
{

    protected $chapter;
    protected $restrictionService;

    /**
     * ChapterRepo constructor.
     * @param Chapter $chapter
     * @param RestrictionService $restrictionService
     */
    public function __construct(Chapter $chapter, RestrictionService $restrictionService)
    {
        $this->chapter = $chapter;
        $this->restrictionService = $restrictionService;
    }

    /**
     * Base query for getting chapters, Takes restrictions into account.
     * @return mixed
     */
    private function chapterQuery()
    {
        return $this->restrictionService->enforceChapterRestrictions($this->chapter, 'view');
    }

    /**
     * Check if an id exists.
     * @param $id
     * @return bool
     */
    public function idExists($id)
    {
        return $this->chapterQuery()->where('id', '=', $id)->count() > 0;
    }

    /**
     * Get a chapter by a specific id.
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->chapterQuery()->findOrFail($id);
    }

    /**
     * Get all chapters.
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll()
    {
        return $this->chapterQuery()->all();
    }

    /**
     * Get a chapter that has the given slug within the given book.
     * @param $slug
     * @param $bookId
     * @return mixed
     * @throws NotFoundException
     */
    public function getBySlug($slug, $bookId)
    {
        $chapter = $this->chapterQuery()->where('slug', '=', $slug)->where('book_id', '=', $bookId)->first();
        if ($chapter === null) throw new NotFoundException('Chapter not found');
        return $chapter;
    }

    /**
     * Get the child items for a chapter
     * @param Chapter $chapter
     */
    public function getChildren(Chapter $chapter)
    {
        return $this->restrictionService->enforcePageRestrictions($chapter->pages())->get();
    }

    /**
     * Create a new chapter from request input.
     * @param $input
     * @return $this
     */
    public function newFromInput($input)
    {
        return $this->chapter->fill($input);
    }

    /**
     * Destroy a chapter and its relations by providing its slug.
     * @param Chapter $chapter
     */
    public function destroy(Chapter $chapter)
    {
        if (count($chapter->pages) > 0) {
            foreach ($chapter->pages as $page) {
                $page->chapter_id = 0;
                $page->save();
            }
        }
        Activity::removeEntity($chapter);
        $chapter->views()->delete();
        $chapter->restrictions()->delete();
        $chapter->delete();
    }

    /**
     * Check if a chapter's slug exists.
     * @param            $slug
     * @param            $bookId
     * @param bool|false $currentId
     * @return bool
     */
    public function doesSlugExist($slug, $bookId, $currentId = false)
    {
        $query = $this->chapter->where('slug', '=', $slug)->where('book_id', '=', $bookId);
        if ($currentId) {
            $query = $query->where('id', '!=', $currentId);
        }
        return $query->count() > 0;
    }

    /**
     * Finds a suitable slug for the provided name.
     * Checks database to prevent duplicate slugs.
     * @param            $name
     * @param            $bookId
     * @param bool|false $currentId
     * @return string
     */
    public function findSuitableSlug($name, $bookId, $currentId = false)
    {
        $slug = Str::slug($name);
        while ($this->doesSlugExist($slug, $bookId, $currentId)) {
            $slug .= '-' . substr(md5(rand(1, 500)), 0, 3);
        }
        return $slug;
    }

    /**
     * Get chapters by the given search term.
     * @param       $term
     * @param array $whereTerms
     * @param int $count
     * @param array $paginationAppends
     * @return mixed
     */
    public function getBySearch($term, $whereTerms = [], $count = 20, $paginationAppends = [])
    {
        $terms = explode(' ', $term);
        $chapters = $this->restrictionService->enforceChapterRestrictions($this->chapter->fullTextSearchQuery(['name', 'description'], $terms, $whereTerms))
            ->paginate($count)->appends($paginationAppends);
        $words = join('|', explode(' ', preg_quote(trim($term), '/')));
        foreach ($chapters as $chapter) {
            //highlight
            $result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $chapter->getExcerpt(100));
            $chapter->searchSnippet = $result;
        }
        return $chapters;
    }

    /**
     * Changes the book relation of this chapter.
     * @param         $bookId
     * @param Chapter $chapter
     * @return Chapter
     */
    public function changeBook($bookId, Chapter $chapter)
    {
        $chapter->book_id = $bookId;
        foreach ($chapter->activity as $activity) {
            $activity->book_id = $bookId;
            $activity->save();
        }
        $chapter->slug = $this->findSuitableSlug($chapter->name, $bookId, $chapter->id);
        $chapter->save();
        return $chapter;
    }

    /**
     * Updates pages restrictions from a request
     * @param $request
     * @param $chapter
     */
    public function updateRestrictionsFromRequest($request, $chapter)
    {
        // TODO - extract into shared repo
        $chapter->restricted = $request->has('restricted') && $request->get('restricted') === 'true';
        $chapter->restrictions()->delete();
        if ($request->has('restrictions')) {
            foreach($request->get('restrictions') as $roleId => $restrictions) {
                foreach ($restrictions as $action => $value) {
                    $chapter->restrictions()->create([
                        'role_id' => $roleId,
                        'action'  => strtolower($action)
                    ]);
                }
            }
        }
        $chapter->save();
    }

}