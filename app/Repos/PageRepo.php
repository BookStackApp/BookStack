<?php namespace BookStack\Repos;

use Activity;
use BookStack\Book;
use BookStack\Chapter;
use BookStack\Exceptions\NotFoundException;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Support\Str;
use BookStack\Page;
use BookStack\PageRevision;

class PageRepo extends EntityRepo
{

    protected $pageRevision;

    /**
     * PageRepo constructor.
     * @param PageRevision $pageRevision
     */
    public function __construct(PageRevision $pageRevision)
    {
        $this->pageRevision = $pageRevision;
        parent::__construct();
    }

    /**
     * Base query for getting pages, Takes restrictions into account.
     * @param bool $allowDrafts
     * @return mixed
     */
    private function pageQuery($allowDrafts = false)
    {
        $query = $this->restrictionService->enforcePageRestrictions($this->page, 'view');
        if (!$allowDrafts) {
            $query = $query->where('draft', '=', false);
        }
        return $query;
    }

    /**
     * Get a page via a specific ID.
     * @param $id
     * @param bool $allowDrafts
     * @return mixed
     */
    public function getById($id, $allowDrafts = false)
    {
        return $this->pageQuery($allowDrafts)->findOrFail($id);
    }

    /**
     * Get a page identified by the given slug.
     * @param $slug
     * @param $bookId
     * @return mixed
     * @throws NotFoundException
     */
    public function getBySlug($slug, $bookId)
    {
        $page = $this->pageQuery()->where('slug', '=', $slug)->where('book_id', '=', $bookId)->first();
        if ($page === null) throw new NotFoundException('Page not found');
        return $page;
    }

    /**
     * Search through page revisions and retrieve
     * the last page in the current book that
     * has a slug equal to the one given.
     * @param $pageSlug
     * @param $bookSlug
     * @return null | Page
     */
    public function findPageUsingOldSlug($pageSlug, $bookSlug)
    {
        $revision = $this->pageRevision->where('slug', '=', $pageSlug)
            ->whereHas('page', function ($query) {
                $this->restrictionService->enforcePageRestrictions($query);
            })
            ->where('type', '=', 'version')
            ->where('book_slug', '=', $bookSlug)->orderBy('created_at', 'desc')
            ->with('page')->first();
        return $revision !== null ? $revision->page : null;
    }

    /**
     * Get a new Page instance from the given input.
     * @param $input
     * @return Page
     */
    public function newFromInput($input)
    {
        $page = $this->page->fill($input);
        return $page;
    }

    /**
     * Count the pages with a particular slug within a book.
     * @param $slug
     * @param $bookId
     * @return mixed
     */
    public function countBySlug($slug, $bookId)
    {
        return $this->page->where('slug', '=', $slug)->where('book_id', '=', $bookId)->count();
    }

    /**
     * Save a new page into the system.
     * Input validation must be done beforehand.
     * @param array $input
     * @param Book $book
     * @param int $chapterId
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
        return $page;
    }


    /**
     * Publish a draft page to make it a normal page.
     * Sets the slug and updates the content.
     * @param Page $draftPage
     * @param array $input
     * @return Page
     */
    public function publishDraft(Page $draftPage, array $input)
    {
        $draftPage->fill($input);

        $draftPage->slug = $this->findSuitableSlug($draftPage->name, $draftPage->book->id);
        $draftPage->html = $this->formatHtml($input['html']);
        $draftPage->text = strip_tags($draftPage->html);
        $draftPage->draft = false;

        $draftPage->save();
        return $draftPage;
    }

    /**
     * Get a new draft page instance.
     * @param Book $book
     * @param Chapter|bool $chapter
     * @return static
     */
    public function getDraftPage(Book $book, $chapter = false)
    {
        $page = $this->page->newInstance();
        $page->name = 'New Page';
        $page->created_by = auth()->user()->id;
        $page->updated_by = auth()->user()->id;
        $page->draft = true;

        if ($chapter) $page->chapter_id = $chapter->id;

        $book->pages()->save($page);
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
        if ($htmlText == '') return $htmlText;
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($htmlText, 'HTML-ENTITIES', 'UTF-8'));

        $container = $doc->documentElement;
        $body = $container->childNodes->item(0);
        $childNodes = $body->childNodes;

        // Ensure no duplicate ids are used
        $idArray = [];

        foreach ($childNodes as $index => $childNode) {
            /** @var \DOMElement $childNode */
            if (get_class($childNode) !== 'DOMElement') continue;

            // Overwrite id if not a BookStack custom id
            if ($childNode->hasAttribute('id')) {
                $id = $childNode->getAttribute('id');
                if (strpos($id, 'bkmrk') === 0 && array_search($id, $idArray) === false) {
                    $idArray[] = $id;
                    continue;
                };
            }

            // Create an unique id for the element
            // Uses the content as a basis to ensure output is the same every time
            // the same content is passed through.
            $contentId = 'bkmrk-' . substr(strtolower(preg_replace('/\s+/', '-', trim($childNode->nodeValue))), 0, 20);
            $newId = urlencode($contentId);
            $loopIndex = 0;
            while (in_array($newId, $idArray)) {
                $newId = urlencode($contentId . '-' . $loopIndex);
                $loopIndex++;
            }

            $childNode->setAttribute('id', $newId);
            $idArray[] = $newId;
        }

        // Generate inner html as a string
        $html = '';
        foreach ($childNodes as $childNode) {
            $html .= $doc->saveHTML($childNode);
        }

        return $html;
    }


    /**
     * Gets pages by a search term.
     * Highlights page content for showing in results.
     * @param string $term
     * @param array $whereTerms
     * @param int $count
     * @param array $paginationAppends
     * @return mixed
     */
    public function getBySearch($term, $whereTerms = [], $count = 20, $paginationAppends = [])
    {
        $terms = $this->prepareSearchTerms($term);
        $pages = $this->restrictionService->enforcePageRestrictions($this->page->fullTextSearchQuery(['name', 'text'], $terms, $whereTerms))
            ->paginate($count)->appends($paginationAppends);

        // Add highlights to page text.
        $words = join('|', explode(' ', preg_quote(trim($term), '/')));
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
        $pages = $this->pageQuery()->where('html', 'like', '%' . $imageString . '%')->get();
        foreach ($pages as $page) {
            $page->url = $page->getUrl();
            $page->html = '';
            $page->text = '';
        }
        return count($pages) > 0 ? $pages : false;
    }

    /**
     * Updates a page with any fillable data and saves it into the database.
     * @param Page $page
     * @param int $book_id
     * @param string $input
     * @return Page
     */
    public function updatePage(Page $page, $book_id, $input)
    {
        // Save a revision before updating
        if ($page->html !== $input['html'] || $page->name !== $input['name']) {
            $this->saveRevision($page);
        }

        // Prevent slug being updated if no name change
        if ($page->name !== $input['name']) {
            $page->slug = $this->findSuitableSlug($input['name'], $book_id, $page->id);
        }

        // Update with new details
        $userId = auth()->user()->id;
        $page->fill($input);
        $page->html = $this->formatHtml($input['html']);
        $page->text = strip_tags($page->html);
        if (setting('app-editor') !== 'markdown') $page->markdown = '';
        $page->updated_by = $userId;
        $page->save();

        // Remove all update drafts for this user & page.
        $this->userUpdateDraftsQuery($page, $userId)->delete();

        return $page;
    }

    /**
     * Restores a revision's content back into a page.
     * @param Page $page
     * @param Book $book
     * @param  int $revisionId
     * @return Page
     */
    public function restoreRevision(Page $page, Book $book, $revisionId)
    {
        $this->saveRevision($page);
        $revision = $this->getRevisionById($revisionId);
        $page->fill($revision->toArray());
        $page->slug = $this->findSuitableSlug($page->name, $book->id, $page->id);
        $page->text = strip_tags($page->html);
        $page->updated_by = auth()->user()->id;
        $page->save();
        return $page;
    }

    /**
     * Saves a page revision into the system.
     * @param Page $page
     * @return $this
     */
    public function saveRevision(Page $page)
    {
        $revision = $this->pageRevision->fill($page->toArray());
        if (setting('app-editor') !== 'markdown') $revision->markdown = '';
        $revision->page_id = $page->id;
        $revision->slug = $page->slug;
        $revision->book_slug = $page->book->slug;
        $revision->created_by = auth()->user()->id;
        $revision->created_at = $page->updated_at;
        $revision->type = 'version';
        $revision->save();
        // Clear old revisions
        if ($this->pageRevision->where('page_id', '=', $page->id)->count() > 50) {
            $this->pageRevision->where('page_id', '=', $page->id)
                ->orderBy('created_at', 'desc')->skip(50)->take(5)->delete();
        }
        return $revision;
    }

    /**
     * Save a page update draft.
     * @param Page $page
     * @param array $data
     * @return PageRevision
     */
    public function saveUpdateDraft(Page $page, $data = [])
    {
        $userId = auth()->user()->id;
        $drafts = $this->userUpdateDraftsQuery($page, $userId)->get();

        if ($drafts->count() > 0) {
            $draft = $drafts->first();
        } else {
            $draft = $this->pageRevision->newInstance();
            $draft->page_id = $page->id;
            $draft->slug = $page->slug;
            $draft->book_slug = $page->book->slug;
            $draft->created_by = $userId;
            $draft->type = 'update_draft';
        }

        $draft->fill($data);
        if (setting('app-editor') !== 'markdown') $draft->markdown = '';
        
        $draft->save();
        return $draft;
    }

    /**
     * Update a draft page.
     * @param Page $page
     * @param array $data
     * @return Page
     */
    public function updateDraftPage(Page $page, $data = [])
    {
        $page->fill($data);

        if (isset($data['html'])) {
            $page->text = strip_tags($data['html']);
        }

        $page->save();
        return $page;
    }

    /**
     * The base query for getting user update drafts.
     * @param Page $page
     * @param $userId
     * @return mixed
     */
    private function userUpdateDraftsQuery(Page $page, $userId)
    {
        return $this->pageRevision->where('created_by', '=', $userId)
            ->where('type', 'update_draft')
            ->where('page_id', '=', $page->id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Checks whether a user has a draft version of a particular page or not.
     * @param Page $page
     * @param $userId
     * @return bool
     */
    public function hasUserGotPageDraft(Page $page, $userId)
    {
        return $this->userUpdateDraftsQuery($page, $userId)->count() > 0;
    }

    /**
     * Get the latest updated draft revision for a particular page and user.
     * @param Page $page
     * @param $userId
     * @return mixed
     */
    public function getUserPageDraft(Page $page, $userId)
    {
        return $this->userUpdateDraftsQuery($page, $userId)->first();
    }

    /**
     * Get the notification message that informs the user that they are editing a draft page.
     * @param PageRevision $draft
     * @return string
     */
    public function getUserPageDraftMessage(PageRevision $draft)
    {
        $message = 'You are currently editing a draft that was last saved ' . $draft->updated_at->diffForHumans() . '.';
        if ($draft->page->updated_at->timestamp > $draft->updated_at->timestamp) {
            $message .= "\n This page has been updated by since that time. It is recommended that you discard this draft.";
        }
        return $message;
    }

    /**
     * Check if a page is being actively editing.
     * Checks for edits since last page updated.
     * Passing in a minuted range will check for edits
     * within the last x minutes.
     * @param Page $page
     * @param null $minRange
     * @return bool
     */
    public function isPageEditingActive(Page $page, $minRange = null)
    {
        $draftSearch = $this->activePageEditingQuery($page, $minRange);
        return $draftSearch->count() > 0;
    }

    /**
     * Get a notification message concerning the editing activity on
     * a particular page.
     * @param Page $page
     * @param null $minRange
     * @return string
     */
    public function getPageEditingActiveMessage(Page $page, $minRange = null)
    {
        $pageDraftEdits = $this->activePageEditingQuery($page, $minRange)->get();
        $userMessage = $pageDraftEdits->count() > 1 ? $pageDraftEdits->count() . ' users have' : $pageDraftEdits->first()->createdBy->name . ' has';
        $timeMessage = $minRange === null ? 'since the page was last updated' : 'in the last ' . $minRange . ' minutes';
        $message = '%s started editing this page %s. Take care not to overwrite each other\'s updates!';
        return sprintf($message, $userMessage, $timeMessage);
    }

    /**
     * A query to check for active update drafts on a particular page.
     * @param Page $page
     * @param null $minRange
     * @return mixed
     */
    private function activePageEditingQuery(Page $page, $minRange = null)
    {
        $query = $this->pageRevision->where('type', '=', 'update_draft')
            ->where('page_id', '=', $page->id)
            ->where('updated_at', '>', $page->updated_at)
            ->where('created_by', '!=', auth()->user()->id)
            ->with('createdBy');

        if ($minRange !== null) {
            $query = $query->where('updated_at', '>=', Carbon::now()->subMinutes($minRange));
        }

        return $query;
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
        if ($currentId) $query = $query->where('id', '!=', $currentId);
        return $query->count() > 0;
    }

    /**
     * Changes the related book for the specified page.
     * Changes the book id of any relations to the page that store the book id.
     * @param int $bookId
     * @param Page $page
     * @return Page
     */
    public function changeBook($bookId, Page $page)
    {
        $page->book_id = $bookId;
        foreach ($page->activity as $activity) {
            $activity->book_id = $bookId;
            $activity->save();
        }
        $page->slug = $this->findSuitableSlug($page->name, $bookId, $page->id);
        $page->save();
        return $page;
    }

    /**
     * Gets a suitable slug for the resource
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
     * Destroy a given page along with its dependencies.
     * @param $page
     */
    public function destroy($page)
    {
        Activity::removeEntity($page);
        $page->views()->delete();
        $page->revisions()->delete();
        $page->restrictions()->delete();
        $page->delete();
    }

    /**
     * Get the latest pages added to the system.
     * @param $count
     */
    public function getRecentlyCreatedPaginated($count = 20)
    {
        return $this->pageQuery()->orderBy('created_at', 'desc')->paginate($count);
    }

    /**
     * Get the latest pages added to the system.
     * @param $count
     */
    public function getRecentlyUpdatedPaginated($count = 20)
    {
        return $this->pageQuery()->orderBy('updated_at', 'desc')->paginate($count);
    }

}
