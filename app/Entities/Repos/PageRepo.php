<?php namespace BookStack\Entities\Repos;

use BookStack\Entities\Book;
use BookStack\Entities\Chapter;
use BookStack\Entities\Entity;
use BookStack\Entities\Page;
use BookStack\Entities\PageRevision;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use DOMXPath;

class PageRepo extends EntityRepo
{

    /**
     * Get page by slug.
     * @param string $pageSlug
     * @param string $bookSlug
     * @return Page
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function getPageBySlug(string $pageSlug, string $bookSlug)
    {
        return $this->getBySlug('page', $pageSlug, $bookSlug);
    }

    /**
     * Search through page revisions and retrieve the last page in the
     * current book that has a slug equal to the one given.
     * @param string $pageSlug
     * @param string $bookSlug
     * @return null|Page
     */
    public function getPageByOldSlug(string $pageSlug, string $bookSlug)
    {
        $revision = $this->entityProvider->pageRevision->where('slug', '=', $pageSlug)
            ->whereHas('page', function ($query) {
                $this->permissionService->enforceEntityRestrictions('page', $query);
            })
            ->where('type', '=', 'version')
            ->where('book_slug', '=', $bookSlug)
            ->orderBy('created_at', 'desc')
            ->with('page')->first();
        return $revision !== null ? $revision->page : null;
    }

    /**
     * Updates a page with any fillable data and saves it into the database.
     * @param Page $page
     * @param int $book_id
     * @param array $input
     * @return Page
     * @throws \Exception
     */
    public function updatePage(Page $page, int $book_id, array $input)
    {
        // Hold the old details to compare later
        $oldHtml = $page->html;
        $oldName = $page->name;

        // Prevent slug being updated if no name change
        if ($page->name !== $input['name']) {
            $page->slug = $this->findSuitableSlug('page', $input['name'], $page->id, $book_id);
        }

        // Save page tags if present
        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($page, $input['tags']);
        }

        // Update with new details
        $userId = user()->id;
        $page->fill($input);
        $page->html = $this->formatHtml($input['html']);
        $page->text = $this->pageToPlainText($page);
        if (setting('app-editor') !== 'markdown') {
            $page->markdown = '';
        }
        $page->updated_by = $userId;
        $page->revision_count++;
        $page->save();

        // Remove all update drafts for this user & page.
        $this->userUpdatePageDraftsQuery($page, $userId)->delete();

        // Save a revision after updating
        if ($oldHtml !== $input['html'] || $oldName !== $input['name'] || $input['summary'] !== null) {
            $this->savePageRevision($page, $input['summary']);
        }

        $this->searchService->indexEntity($page);

        return $page;
    }

    /**
     * Saves a page revision into the system.
     * @param Page $page
     * @param null|string $summary
     * @return PageRevision
     * @throws \Exception
     */
    public function savePageRevision(Page $page, string $summary = null)
    {
        $revision = $this->entityProvider->pageRevision->newInstance($page->toArray());
        if (setting('app-editor') !== 'markdown') {
            $revision->markdown = '';
        }
        $revision->page_id = $page->id;
        $revision->slug = $page->slug;
        $revision->book_slug = $page->book->slug;
        $revision->created_by = user()->id;
        $revision->created_at = $page->updated_at;
        $revision->type = 'version';
        $revision->summary = $summary;
        $revision->revision_number = $page->revision_count;
        $revision->save();

        $revisionLimit = config('app.revision_limit');
        if ($revisionLimit !== false) {
            $revisionsToDelete = $this->entityProvider->pageRevision->where('page_id', '=', $page->id)
                ->orderBy('created_at', 'desc')->skip(intval($revisionLimit))->take(10)->get(['id']);
            if ($revisionsToDelete->count() > 0) {
                $this->entityProvider->pageRevision->whereIn('id', $revisionsToDelete->pluck('id'))->delete();
            }
        }

        return $revision;
    }

    /**
     * Formats a page's html to be tagged correctly within the system.
     * @param string $htmlText
     * @return string
     */
    protected function formatHtml(string $htmlText)
    {
        if ($htmlText == '') {
            return $htmlText;
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($htmlText, 'HTML-ENTITIES', 'UTF-8'));

        $container = $doc->documentElement;
        $body = $container->childNodes->item(0);
        $childNodes = $body->childNodes;

        // Set ids on top-level nodes
        $idMap = [];
        foreach ($childNodes as $index => $childNode) {
            $this->setUniqueId($childNode, $idMap);
        }

        // Ensure no duplicate ids within child items
        $xPath = new DOMXPath($doc);
        $idElems = $xPath->query('//body//*//*[@id]');
        foreach ($idElems as $domElem) {
            $this->setUniqueId($domElem, $idMap);
        }

        // Generate inner html as a string
        $html = '';
        foreach ($childNodes as $childNode) {
            $html .= $doc->saveHTML($childNode);
        }

        return $html;
    }

    /**
     * Set a unique id on the given DOMElement.
     * A map for existing ID's should be passed in to check for current existence.
     * @param DOMElement $element
     * @param array $idMap
     */
    protected function setUniqueId($element, array &$idMap)
    {
        if (get_class($element) !== 'DOMElement') {
            return;
        }

        // Overwrite id if not a BookStack custom id
        $existingId = $element->getAttribute('id');
        if (strpos($existingId, 'bkmrk') === 0 && !isset($idMap[$existingId])) {
            $idMap[$existingId] = true;
            return;
        }

        // Create an unique id for the element
        // Uses the content as a basis to ensure output is the same every time
        // the same content is passed through.
        $contentId = 'bkmrk-' . substr(strtolower(preg_replace('/\s+/', '-', trim($element->nodeValue))), 0, 20);
        $newId = urlencode($contentId);
        $loopIndex = 0;

        while (isset($idMap[$newId])) {
            $newId = urlencode($contentId . '-' . $loopIndex);
            $loopIndex++;
        }

        $element->setAttribute('id', $newId);
        $idMap[$newId] = true;
    }

    /**
     * Get the plain text version of a page's content.
     * @param \BookStack\Entities\Page $page
     * @return string
     */
    protected function pageToPlainText(Page $page) : string
    {
        $html = $this->renderPage($page, true);
        return strip_tags($html);
    }

    /**
     * Get a new draft page instance.
     * @param Book $book
     * @param Chapter|null $chapter
     * @return \BookStack\Entities\Page
     * @throws \Throwable
     */
    public function getDraftPage(Book $book, Chapter $chapter = null)
    {
        $page = $this->entityProvider->page->newInstance();
        $page->name = trans('entities.pages_initial_name');
        $page->created_by = user()->id;
        $page->updated_by = user()->id;
        $page->draft = true;

        if ($chapter) {
            $page->chapter_id = $chapter->id;
        }

        $book->pages()->save($page);
        $page = $this->entityProvider->page->find($page->id);
        $this->permissionService->buildJointPermissionsForEntity($page);
        return $page;
    }

    /**
     * Save a page update draft.
     * @param Page $page
     * @param array $data
     * @return PageRevision|Page
     */
    public function updatePageDraft(Page $page, array $data = [])
    {
        // If the page itself is a draft simply update that
        if ($page->draft) {
            $page->fill($data);
            if (isset($data['html'])) {
                $page->text = $this->pageToPlainText($page);
            }
            $page->save();
            return $page;
        }

        // Otherwise save the data to a revision
        $userId = user()->id;
        $drafts = $this->userUpdatePageDraftsQuery($page, $userId)->get();

        if ($drafts->count() > 0) {
            $draft = $drafts->first();
        } else {
            $draft = $this->entityProvider->pageRevision->newInstance();
            $draft->page_id = $page->id;
            $draft->slug = $page->slug;
            $draft->book_slug = $page->book->slug;
            $draft->created_by = $userId;
            $draft->type = 'update_draft';
        }

        $draft->fill($data);
        if (setting('app-editor') !== 'markdown') {
            $draft->markdown = '';
        }

        $draft->save();
        return $draft;
    }

    /**
     * Publish a draft page to make it a normal page.
     * Sets the slug and updates the content.
     * @param Page $draftPage
     * @param array $input
     * @return Page
     * @throws \Exception
     */
    public function publishPageDraft(Page $draftPage, array $input)
    {
        $draftPage->fill($input);

        // Save page tags if present
        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($draftPage, $input['tags']);
        }

        $draftPage->slug = $this->findSuitableSlug('page', $draftPage->name, false, $draftPage->book->id);
        $draftPage->html = $this->formatHtml($input['html']);
        $draftPage->text = $this->pageToPlainText($draftPage);
        $draftPage->draft = false;
        $draftPage->revision_count = 1;

        $draftPage->save();
        $this->savePageRevision($draftPage, trans('entities.pages_initial_revision'));
        $this->searchService->indexEntity($draftPage);
        return $draftPage;
    }

    /**
     * The base query for getting user update drafts.
     * @param Page $page
     * @param $userId
     * @return mixed
     */
    protected function userUpdatePageDraftsQuery(Page $page, int $userId)
    {
        return $this->entityProvider->pageRevision->where('created_by', '=', $userId)
            ->where('type', 'update_draft')
            ->where('page_id', '=', $page->id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest updated draft revision for a particular page and user.
     * @param Page $page
     * @param $userId
     * @return PageRevision|null
     */
    public function getUserPageDraft(Page $page, int $userId)
    {
        return $this->userUpdatePageDraftsQuery($page, $userId)->first();
    }

    /**
     * Get the notification message that informs the user that they are editing a draft page.
     * @param PageRevision $draft
     * @return string
     */
    public function getUserPageDraftMessage(PageRevision $draft)
    {
        $message = trans('entities.pages_editing_draft_notification', ['timeDiff' => $draft->updated_at->diffForHumans()]);
        if ($draft->page->updated_at->timestamp <= $draft->updated_at->timestamp) {
            return $message;
        }
        return $message . "\n" . trans('entities.pages_draft_edited_notification');
    }

    /**
     * A query to check for active update drafts on a particular page.
     * @param Page $page
     * @param int $minRange
     * @return mixed
     */
    protected function activePageEditingQuery(Page $page, int $minRange = null)
    {
        $query = $this->entityProvider->pageRevision->where('type', '=', 'update_draft')
            ->where('page_id', '=', $page->id)
            ->where('updated_at', '>', $page->updated_at)
            ->where('created_by', '!=', user()->id)
            ->with('createdBy');

        if ($minRange !== null) {
            $query = $query->where('updated_at', '>=', Carbon::now()->subMinutes($minRange));
        }

        return $query;
    }

    /**
     * Check if a page is being actively editing.
     * Checks for edits since last page updated.
     * Passing in a minuted range will check for edits
     * within the last x minutes.
     * @param Page $page
     * @param int $minRange
     * @return bool
     */
    public function isPageEditingActive(Page $page, int $minRange = null)
    {
        $draftSearch = $this->activePageEditingQuery($page, $minRange);
        return $draftSearch->count() > 0;
    }

    /**
     * Get a notification message concerning the editing activity on a particular page.
     * @param Page $page
     * @param int $minRange
     * @return string
     */
    public function getPageEditingActiveMessage(Page $page, int $minRange = null)
    {
        $pageDraftEdits = $this->activePageEditingQuery($page, $minRange)->get();

        $userMessage = $pageDraftEdits->count() > 1 ? trans('entities.pages_draft_edit_active.start_a', ['count' => $pageDraftEdits->count()]): trans('entities.pages_draft_edit_active.start_b', ['userName' => $pageDraftEdits->first()->createdBy->name]);
        $timeMessage = $minRange === null ? trans('entities.pages_draft_edit_active.time_a') : trans('entities.pages_draft_edit_active.time_b', ['minCount'=>$minRange]);
        return trans('entities.pages_draft_edit_active.message', ['start' => $userMessage, 'time' => $timeMessage]);
    }

    /**
     * Parse the headers on the page to get a navigation menu
     * @param string $pageContent
     * @return array
     */
    public function getPageNav(string $pageContent)
    {
        if ($pageContent == '') {
            return [];
        }
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($pageContent, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);
        $headers = $xPath->query("//h1|//h2|//h3|//h4|//h5|//h6");

        if (is_null($headers)) {
            return [];
        }

        $tree = collect($headers)->map(function($header) {
            $text = trim(str_replace("\xc2\xa0", '', $header->nodeValue));
            if (strlen($text) > 30) {
                $text = substr($text, 0, 27) . '...';
            }

            return [
                'nodeName' => strtolower($header->nodeName),
                'level' => intval(str_replace('h', '', $header->nodeName)),
                'link' => '#' . $header->getAttribute('id'),
                'text' => $text,
            ];
        })->filter(function($header) {
            return strlen($header['text']) > 0;
        });

        // Normalise headers if only smaller headers have been used
        $minLevel = $tree->pluck('level')->min();
        $tree = $tree->map(function ($header) use ($minLevel) {
            $header['level'] -= ($minLevel - 2);
            return $header;
        });

        return $tree->toArray();
    }

    /**
     * Restores a revision's content back into a page.
     * @param Page $page
     * @param Book $book
     * @param  int $revisionId
     * @return Page
     * @throws \Exception
     */
    public function restorePageRevision(Page $page, Book $book, int $revisionId)
    {
        $page->revision_count++;
        $this->savePageRevision($page);
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();
        $page->fill($revision->toArray());
        $page->slug = $this->findSuitableSlug('page', $page->name, $page->id, $book->id);
        $page->text = $this->pageToPlainText($page);
        $page->updated_by = user()->id;
        $page->save();
        $this->searchService->indexEntity($page);
        return $page;
    }

    /**
     * Change the page's parent to the given entity.
     * @param Page $page
     * @param Entity $parent
     * @throws \Throwable
     */
    public function changePageParent(Page $page, Entity $parent)
    {
        $book = $parent->isA('book') ? $parent : $parent->book;
        $page->chapter_id = $parent->isA('chapter') ? $parent->id : 0;
        $page->save();
        if ($page->book->id !== $book->id) {
            $page = $this->changeBook('page', $book->id, $page);
        }
        $page->load('book');
        $this->permissionService->buildJointPermissionsForEntity($book);
    }

    /**
     * Create a copy of a page in a new location with a new name.
     * @param \BookStack\Entities\Page $page
     * @param \BookStack\Entities\Entity $newParent
     * @param string $newName
     * @return \BookStack\Entities\Page
     * @throws \Throwable
     */
    public function copyPage(Page $page, Entity $newParent, string $newName = '')
    {
        $newBook = $newParent->isA('book') ? $newParent : $newParent->book;
        $newChapter = $newParent->isA('chapter') ? $newParent : null;
        $copyPage = $this->getDraftPage($newBook, $newChapter);
        $pageData = $page->getAttributes();

        // Update name
        if (!empty($newName)) {
            $pageData['name'] = $newName;
        }

        // Copy tags from previous page if set
        if ($page->tags) {
            $pageData['tags'] = [];
            foreach ($page->tags as $tag) {
                $pageData['tags'][] = ['name' => $tag->name, 'value' => $tag->value];
            }
        }

        // Set priority
        if ($newParent->isA('chapter')) {
            $pageData['priority'] = $this->getNewChapterPriority($newParent);
        } else {
            $pageData['priority'] = $this->getNewBookPriority($newParent);
        }

        return $this->publishPageDraft($copyPage, $pageData);
    }
}
