<?php namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Entity;
use Illuminate\Support\Collection;

/**
 * Finds the next or previous content of a book element (page or chapter).
 */
class NextPreviousContentLocator
{
    protected $relativeBookItem;
    protected $flatTree;
    protected $currentIndex = null;

    /**
     * NextPreviousContentLocator constructor.
     */
    public function __construct(BookChild $relativeBookItem, Collection $bookTree)
    {
        $this->relativeBookItem = $relativeBookItem;
        $this->flatTree = $this->treeToFlatOrderedCollection($bookTree);
        $this->currentIndex = $this->getCurrentIndex();
    }

    /**
     * Get the next logical entity within the book hierarchy.
     */
    public function getNext(): ?Entity
    {
        return $this->flatTree->get($this->currentIndex + 1);
    }

    /**
     * Get the next logical entity within the book hierarchy.
     */
    public function getPrevious(): ?Entity
    {
        return $this->flatTree->get($this->currentIndex - 1);
    }

    /**
     * Get the index of the current relative item.
     */
    protected function getCurrentIndex(): ?int
    {
        $index = $this->flatTree->search(function (Entity $entity) {
            return get_class($entity) === get_class($this->relativeBookItem)
                && $entity->id === $this->relativeBookItem->id;
        });
        return $index === false ? null : $index;
    }

    /**
     * Convert a book tree collection to a flattened version
     * where all items follow the expected order of user flow.
     */
    protected function treeToFlatOrderedCollection(Collection $bookTree): Collection
    {
        $flatOrdered = collect();
        /** @var Entity $item */
        foreach ($bookTree->all() as $item) {
            $flatOrdered->push($item);
            $childPages = $item->visible_pages ?? [];
            $flatOrdered = $flatOrdered->concat($childPages);
        }
        return $flatOrdered;
    }
}
