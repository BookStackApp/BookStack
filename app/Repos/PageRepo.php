<?php namespace BookStack\Repos;


use BookStack\Book;
use BookStack\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use BookStack\Page;
use BookStack\PageRevision;

class PageRepo
{
    protected $page;
    protected $pageRevision;

    /**
     * PageRepo constructor.
     * @param Page         $page
     * @param PageRevision $pageRevision
     */
    public function __construct(Page $page, PageRevision $pageRevision)
    {
        $this->page = $page;
        $this->pageRevision = $pageRevision;
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

    /**
     * @param $input
     * @return Page
     */
    public function newFromInput($input)
    {
        $page = $this->page->fill($input);
        return $page;
    }

    public function countBySlug($slug, $bookId)
    {
        return $this->page->where('slug', '=', $slug)->where('book_id', '=', $bookId)->count();
    }

    /**
     * Save a new page into the system.
     * Input validation must be done beforehand.
     * @param array $input
     * @param Book  $book
     * @param int   $chapterId
     * @return Page
     */
    public function saveNew(array $input, Book $book, $chapterId = null)
    {
        $page = $this->newFromInput($input);
        $page->slug = $this->findSuitableSlug($page->name, $book->id);

        if ($chapterId) $page->chapter_id = $chapterId;

        $page->html = $this->formatHtml($input['html']);
        $page->text = strip_tags($page->html);
        $page->created_by = auth()->user()->id;
        $page->updated_by = auth()->user()->id;

        $book->pages()->save($page);
        $this->saveRevision($page);
        return $page;
    }

    /**
     * Formats a page's html to be tagged correctly
     * within the system.
     * @param string $htmlText
     * @return string
     */
    protected function formatHtml($htmlText)
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($htmlText);

        $container = $doc->documentElement;
        $body = $container->childNodes[0];
        $childNodes = $body->childNodes;

        // Ensure no duplicate ids are used
        $lastId = false;
        $idArray = [];

        foreach ($childNodes as $index => $childNode) {
            /** @var \DOMElement $childNode */
            if (get_class($childNode) !== 'DOMElement') continue;

            // Overwrite id if not a bookstack custom id
            if ($childNode->hasAttribute('id')) {
                $id = $childNode->getAttribute('id');
                if (strpos($id, 'bkmrk') === 0 && array_search($id, $idArray) === false) {
                    $idArray[] = $id;
                    continue;
                };
            }

            // Create an unique id for the element
            do {
                $id = 'bkmrk-' . substr(uniqid(), -5);
            } while ($id == $lastId);
            $lastId = $id;

            $childNode->setAttribute('id', $id);
            $idArray[] = $id;
        }

        // Generate inner html as a string
        $html = '';
        foreach ($childNodes as $childNode) {
            $html .= $doc->saveHTML($childNode);
        }

        return $html;
    }

    public function destroyById($id)
    {
        $page = $this->getById($id);
        $page->delete();
    }

    public function getBySearch($term, $whereTerms = [])
    {
        $terms = explode(' ', preg_quote(trim($term)));
        $pages = $this->page->fullTextSearch(['name', 'text'], $terms, $whereTerms);

        // Add highlights to page text.
        $words = join('|', $terms);
        //lookahead/behind assertions ensures cut between words
        $s = '\s\x00-/:-@\[-`{-~'; //character set for start/end of words

        foreach ($pages as $page) {
            preg_match_all('#(?<=[' . $s . ']).{1,30}((' . $words . ').{1,30})+(?=[' . $s . '])#uis', $page->text, $matches, PREG_SET_ORDER);
            //delimiter between occurrences
            $results = [];
            foreach ($matches as $line) {
                $results[] = htmlspecialchars($line[0], 0, 'UTF-8');
            }
            $matchLimit = 6;
            if (count($results) > $matchLimit) {
                $results = array_slice($results, 0, $matchLimit);
            }
            $result = join('... ', $results);

            //highlight
            $result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $result);
            if (strlen($result) < 5) {
                $result = $page->getExcerpt(80);
            }
            $page->searchSnippet = $result;
        }
        return $pages;
    }

    /**
     * Search for image usage.
     * @param $imageString
     * @return mixed
     */
    public function searchForImage($imageString)
    {
        $pages = $this->page->where('html', 'like', '%' . $imageString . '%')->get();
        foreach ($pages as $page) {
            $page->url = $page->getUrl();
            $page->html = '';
            $page->text = '';
        }
        return count($pages) > 0 ? $pages : false;
    }

    /**
     * Updates a page with any fillable data and saves it into the database.
     * @param Page   $page
     * @param int    $book_id
     * @param string $input
     * @return Page
     */
    public function updatePage(Page $page, $book_id, $input)
    {
        $page->fill($input);
        $page->slug = $this->findSuitableSlug($page->name, $book_id, $page->id);
        $page->html = $this->formatHtml($input['html']);
        $page->text = strip_tags($page->html);
        $page->updated_by = Auth::user()->id;
        $page->save();
        $this->saveRevision($page);
        return $page;
    }

    /**
     * Saves a page revision into the system.
     * @param Page $page
     * @return $this
     */
    public function saveRevision(Page $page)
    {
        $lastRevision = $this->getLastRevision($page);
        if ($lastRevision && ($lastRevision->html === $page->html && $lastRevision->name === $page->name)) {
            return $page;
        }
        $revision = $this->pageRevision->fill($page->toArray());
        $revision->page_id = $page->id;
        $revision->created_by = Auth::user()->id;
        $revision->save();
        // Clear old revisions
        if ($this->pageRevision->where('page_id', '=', $page->id)->count() > 50) {
            $this->pageRevision->where('page_id', '=', $page->id)
                ->orderBy('created_at', 'desc')->skip(50)->take(5)->delete();
        }
        return $revision;
    }

    /**
     * Gets the most recent revision for a page.
     * @param Page $page
     * @return mixed
     */
    public function getLastRevision(Page $page)
    {
        return $this->pageRevision->where('page_id', '=', $page->id)
            ->orderBy('created_at', 'desc')->first();
    }

    /**
     * Gets a single revision via it's id.
     * @param $id
     * @return mixed
     */
    public function getRevisionById($id)
    {
        return $this->pageRevision->findOrFail($id);
    }

    /**
     * Checks if a slug exists within a book already.
     * @param            $slug
     * @param            $bookId
     * @param bool|false $currentId
     * @return bool
     */
    public function doesSlugExist($slug, $bookId, $currentId = false)
    {
        $query = $this->page->where('slug', '=', $slug)->where('book_id', '=', $bookId);
        if ($currentId) {
            $query = $query->where('id', '!=', $currentId);
        }
        return $query->count() > 0;
    }

    public function setBookId($bookId, Page $page)
    {
        $page->book_id = $bookId;
        foreach ($page->activity as $activity) {
            $activity->book_id = $bookId;
            $activity->save();
        }
        $page->save();
        return $page;
    }

    /**
     * Gets a suitable slug for the resource
     *
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


}