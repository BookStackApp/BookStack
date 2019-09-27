<?php namespace BookStack\Entities\Actions;

use BookStack\Entities\Book;
use BookStack\Entities\Chapter;
use BookStack\Entities\Entity;
use BookStack\Entities\Page;
use Illuminate\Support\Collection;

class BookContents
{

    /**
     * @var Book
     */
    protected $book;

    /**
     * BookContents constructor.
     * @param $book
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Get the current priority of the last item
     * at the top-level of the book.
     */
    public function getLastPriority(): int
    {
        $maxPage = Page::visible()->where('book_id', '=', $this->book->id)
            ->where('draft', '=', false)
            ->where('chapter_id', '=', 0)->max('priority');
        $maxChapter = Chapter::visible()->where('book_id', '=', $this->book->id)
            ->where('chapter_id', '=', 0)->max('priority');
        return max($maxChapter, $maxPage, 0);
    }

    /**
     * Get the contents as a sorted collection tree.
     * TODO - Support $renderPages option
     */
    public function getTree(bool $filterDrafts = false, bool $renderPages = false): Collection
    {
        $pages = $this->getPages($filterDrafts);
        $chapters = Chapter::visible()->where('book_id', '=', $this->book->id)->get();
        $all = collect()->concat($pages)->concat($chapters);
        $chapterMap = $chapters->keyBy('id');
        $lonePages = collect();

        $pages->groupBy('chapter_id')->each(function($pages, $chapter_id) use ($chapterMap, &$lonePages) {
            $chapter = $chapterMap->get($chapter_id);
            if ($chapter) {
                $chapter->setAttribute('pages', collect($pages)->sortBy($this->bookChildSortFunc()));
            } else {
                $lonePages = $lonePages->concat($pages);
            }
        });

        $all->each(function(Entity $entity) {
            $entity->setRelation('book', $this->book);
        });

        return collect($chapters)->concat($lonePages)->sortBy($this->bookChildSortFunc());
    }

    /**
     * Function for providing a sorting score for an entity in relation to the
     * other items within the book.
     */
    protected function bookChildSortFunc(): callable
    {
        return function(Entity $entity) {
            if (isset($entity['draft']) && $entity['draft']) {
                return -100;
            }
            return $entity['priority'] ?? 0;
        };
    }

    /**
     * Get the visible pages within this book.
     */
    protected function getPages(bool $filterDrafts = false): Collection
    {
        $query = Page::visible()->where('book_id', '=', $this->book->id);

        if ($filterDrafts) {
            $query->where('draft', '=', false);
        }

        return $query->get();
    }

}