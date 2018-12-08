<?php

namespace BookStack\Actions;

use BookStack\Auth\User;
use BookStack\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use BookStack\Entities\PageRevision;

/**
 * @property string  key
 * @property \User   user
 * @property \Entity entity
 * @property string  extra
 */
class Activity extends Model implements Feedable
{

    /**
     * Get the entity for this activity.
     */
    public function entity()
    {
        if ($this->entity_type === '') {
            $this->entity_type = null;
        }
        return $this->morphTo('entity');
    }

    /**
     * Get the user this activity relates to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns text from the language files, Looks up by using the
     * activity key.
     */
    public function getText()
    {
        return trans('activities.' . $this->key);
    }

    /**
     * Checks if another Activity matches the general information of another.
     * @param $activityB
     * @return bool
     */
    public function isSimilarTo($activityB)
    {
        return [$this->key, $this->entity_type, $this->entity_id] === [$activityB->key, $activityB->entity_type, $activityB->entity_id];
    }
    
    public function toFeedItem()
    {
                
        $summary = $this->user->getShortName() . ' ' . $this->getText();
        $title = $summary;
        
        if ($this->entity) {
            $summary .= ' ' . '<a href="' . $this->entity->getUrl() . '">' . $this->entity->name . '</a>';
            $title .= ' |' . $this->entity->name . '|';
            $link = $this->entity->getUrl();
        }
        
        if ($this->key == 'page_update') {
            $rev = $this->entity->revisions->first();
            $summary .= ' | <a href="' . $rev->getUrl('changes') . '">' . trans('entities.pages_revisions_changes') . '</a>' ;
            $link = $rev->getUrl('changes');
        }
        
        return FeedItem::create([
            'id' => $this->id,
            'title' => $title,
            'summary' => $summary,
            'updated' => $this->updated_at,
            'link' => $link,
            'author' => $this->user->getShortName(),
        ]);
    }
    
    public static function getFeedItems(\BookStack\Entities\Repos\PageRepo $pageRepo, \BookStack\Entities\Repos\EntityRepo $entityRepo)
    {
        # page subscription
        if (request()->route()->parameter('pageSlug')) {
            $pageSlug = request()->route()->parameter('pageSlug');
            $bookSlug = request()->route()->parameter('bookSlug');
            $page = $pageRepo->getPageBySlug($pageSlug, $bookSlug);        
            return Activity::where('key', 'like', 'page_%')->where('entity_id', $page->id)->get();
        } 

        # chapter subscription
        if (request()->route()->parameter('chapterSlug')) {
            $chapterSlug = request()->route()->parameter('chapterSlug');
            $bookSlug = request()->route()->parameter('bookSlug');
            $chapter = $entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
            $pages = $entityRepo->getChapterChildren($chapter);
            $pageIds = [];
            foreach ($pages as $page) { $pageIds[] = $page->id; }
            return Activity::
                    where(function ($query) use ($chapter) {
                        $query->where('key', 'like', 'chapter_%')
                        ->where('entity_id', $chapter->id);
                    })
                    ->orWhere(function ($query) use ($pageIds) {
                        $query->where('key', 'like', 'page_%')
                        ->whereIn('entity_id', $pageIds);
                    })
                    ->get();
        }

        # book subscription
        if (request()->route()->parameter('bookSlug')) {
            $bookSlug = request()->route()->parameter('bookSlug');
            $book = $entityRepo->getBySlug('book', $bookSlug);
            $children = $entityRepo->getBookChildren($book);
            foreach ($children as $child) { 
                if (is_a($child, 'BookStack\Entities\Page')) {
                    $pageIds[] = $child->id; 
                    continue;
                }
                $pages = $entityRepo->getChapterChildren($child);
                foreach ($pages as $page) { $pageIds[] = $page->id; }
            }
            return Activity::
                    where(function ($query) use ($book) {
                        $query->where('key', 'like', 'book_%')
                        ->where('entity_id', $book->id);
                    })
                    ->orWhere(function ($query) use ($pageIds) {
                        $query->where('key', 'like', 'page_%')
                        ->whereIn('entity_id', $pageIds);
                    })
                    ->get();
        }
        
       return Activity::all();
    }
}
