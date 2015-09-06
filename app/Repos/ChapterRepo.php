<?php namespace Oxbow\Repos;


use Illuminate\Support\Str;
use Oxbow\Chapter;

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

    public function idExists($id)
    {
        return $this->chapter->where('id', '=', $id)->count() > 0;
    }

    public function getById($id)
    {
        return $this->chapter->findOrFail($id);
    }

    public function getAll()
    {
        return $this->chapter->all();
    }

    public function getBySlug($slug, $bookId)
    {
        return $this->chapter->where('slug', '=', $slug)->where('book_id', '=', $bookId)->first();
    }

    public function newFromInput($input)
    {
        return $this->chapter->fill($input);
    }

    public function destroyById($id)
    {
        $page = $this->getById($id);
        $page->delete();
    }

    public function doesSlugExist($slug, $bookId, $currentId = false)
    {
        $query = $this->chapter->where('slug', '=', $slug)->where('book_id', '=', $bookId);
        if($currentId) {
            $query = $query->where('id', '!=', $currentId);
        }
        return $query->count() > 0;
    }

    public function findSuitableSlug($name, $bookId, $currentId = false)
    {
        $slug = Str::slug($name);
        while($this->doesSlugExist($slug, $bookId, $currentId)) {
            $slug .= '-' . substr(md5(rand(1, 500)), 0, 3);
        }
        return $slug;
    }

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