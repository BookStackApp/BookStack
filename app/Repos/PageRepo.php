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

    public function getById($id)
    {
        return $this->page->findOrFail($id);
    }

    public function getAll()
    {
        return $this->page->all();
    }

    public function getBySlug($slug)
    {
        return $this->page->where('slug', '=', $slug)->first();
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

}