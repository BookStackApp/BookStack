<?php namespace Oxbow\Repos;


use Illuminate\Support\Str;
use Oxbow\Page;

class PageRepo
{
    protected $page;

    /**
     * PageRepo constructor.
     * @param $page
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function idExists($id)
    {
        return $this->page->where('page_id', '=', $id)->count() > 0;
    }

    public function getById($id)
    {
        return $this->page->findOrFail($id);
    }

    public function getAll()
    {
        return $this->page->all();
    }

    public function getBySlug($slug, $bookId)
    {
        return $this->page->where('slug', '=', $slug)->where('book_id', '=', $bookId)->first();
    }

    public function newFromInput($input)
    {
        $page = $this->page->fill($input);
        return $page;
    }

    public function countBySlug($slug, $bookId)
    {
        return $this->page->where('slug', '=', $slug)->where('book_id', '=', $bookId)->count();
    }

    public function destroyById($id)
    {
        $page = $this->getById($id);
        $page->delete();
    }

    public function getBySearch($term)
    {
        $terms = explode(' ', trim($term));
        $query = $this->page;
        foreach($terms as $term) {
            $query = $query->where('text', 'like', '%'.$term.'%');
        }
        return $query->get();
    }

    /**
     * Checks if a slug exists within a book already.
     * @param $slug
     * @param $bookId
     * @param bool|false $currentId
     * @return bool
     */
    public function doesSlugExist($slug, $bookId, $currentId = false)
    {
        $query = $this->page->where('slug', '=', $slug)->where('book_id', '=', $bookId);
        if($currentId) {
            $query = $query->where('id', '!=', $currentId);
        }
        return $query->count() > 0;
    }

    /**
     * Gets a suitable slug for the resource
     *
     * @param $name
     * @param $bookId
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


}