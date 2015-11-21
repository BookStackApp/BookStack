<?php namespace BookStack\Repos;


use Activity;
use Illuminate\Support\Str;
use BookStack\Chapter;

class ChapterRepo
{

    protected $chapter;

    /**
     * ChapterRepo constructor.
     * @param $chapter
     */
    public function __construct(Chapter $chapter)
    {
        $this->chapter = $chapter;
    }

    /**
     * Check if an id exists.
     * @param $id
     * @return bool
     */
    public function idExists($id)
    {
        return $this->chapter->where('id', '=', $id)->count() > 0;
    }

    /**
     * Get a chapter by a specific id.
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->chapter->findOrFail($id);
    }

    /**
     * Get all chapters.
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll()
    {
        return $this->chapter->all();
    }

    /**
     * Get a chapter that has the given slug within the given book.
     * @param $slug
     * @param $bookId
     * @return mixed
     */
    public function getBySlug($slug, $bookId)
    {
        return $this->chapter->where('slug', '=', $slug)->where('book_id', '=', $bookId)->first();
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
        if($currentId) {
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
        while($this->doesSlugExist($slug, $bookId, $currentId)) {
            $slug .= '-' . substr(md5(rand(1, 500)), 0, 3);
        }
        return $slug;
    }

    /**
     * Get chapters by the given search term.
     * @param       $term
     * @param array $whereTerms
     * @return mixed
     */
    public function getBySearch($term, $whereTerms = [])
    {
        $terms = explode(' ', preg_quote(trim($term)));
        $chapters = $this->chapter->fullTextSearch(['name', 'description'], $terms, $whereTerms);
        $words = join('|', $terms);
        foreach ($chapters as $chapter) {
            //highlight
            $result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $chapter->getExcerpt(100));
            $chapter->searchSnippet = $result;
        }
        return $chapters;
    }

    /**
     * Sets a chapters book id.
     * @param         $bookId
     * @param Chapter $chapter
     * @return Chapter
     */
    public function setBookId($bookId, Chapter $chapter)
    {
        $chapter->book_id = $bookId;
        foreach($chapter->activity as $activity) {
            $activity->book_id = $bookId;
            $activity->save();
        }
        $chapter->save();
        return $chapter;
    }

}