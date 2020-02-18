<?php namespace BookStack\Entities\Managers;

use BookStack\Auth\User;
use BookStack\Entities\Page;
use BookStack\Entities\PageRevision;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PageEditActivity
{

    protected $page;

    /**
     * PageEditActivity constructor.
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * Check if there's active editing being performed on this page.
     * @return bool
     */
    public function hasActiveEditing(): bool
    {
        return $this->activePageEditingQuery(60)->count() > 0;
    }

    /**
     * Get a notification message concerning the editing activity on the page.
     */
    public function activeEditingMessage(): string
    {
        $pageDraftEdits = $this->activePageEditingQuery(60)->get();
        $count = $pageDraftEdits->count();

        $userMessage = $count > 1 ? trans('entities.pages_draft_edit_active.start_a', ['count' => $count]): trans('entities.pages_draft_edit_active.start_b', ['userName' => $pageDraftEdits->first()->createdBy->name]);
        $timeMessage = trans('entities.pages_draft_edit_active.time_b', ['minCount'=> 60]);
        return trans('entities.pages_draft_edit_active.message', ['start' => $userMessage, 'time' => $timeMessage]);
    }

    /**
     * Get the message to show when the user will be editing one of the drafts.
     * @param PageRevision $draft
     * @return string
     */
    public function getEditingActiveDraftMessage(PageRevision $draft, bool $sharedDrafts): string
    {
        if ($sharedDrafts) {
            $time = $draft->updated_at->diffForHumans();
            $user = user();
            error_log('$draft->updated_by: ' . $draft->updated_by);
            error_log('$user->id: ' . $user->id);
            if ($draft->created_by === $user->id) {
                return trans('entities.pages_editing_shared_draft_notification.message', ['timeDiff' => $time, 'userName' => trans('entities.pages_editing_shared_draft_notification.you')]);
            }
            $createdUser = User::find($draft->created_by);
            $userName = $createdUser ? $createdUser->name : '_user'.$draft->created_by.'_';
            return trans('entities.pages_editing_shared_draft_notification.message', ['timeDiff' => $time, 'userName' => $userName]) . "\n" .
                trans('entities.pages_editing_shared_draft_notification.warn');
        }
        $message = trans('entities.pages_editing_draft_notification', ['timeDiff' => $draft->updated_at->diffForHumans()]);
        if ($draft->page->updated_at->timestamp <= $draft->updated_at->timestamp) {
            return $message;
        }
        return $message . "\n" . trans('entities.pages_draft_edited_notification');
    }

    /**
     * A query to check for active update drafts on a particular page
     * within the last given many minutes.
     */
    protected function activePageEditingQuery(int $withinMinutes): Builder
    {
        $checkTime = Carbon::now()->subMinutes($withinMinutes);
        $query = PageRevision::query()
            ->where('type', '=', 'update_draft')
            ->where('page_id', '=', $this->page->id)
            ->where('updated_at', '>', $this->page->updated_at)
            ->where('created_by', '!=', user()->id)
            ->where('updated_at', '>=', $checkTime)
            ->with('createdBy');

        return $query;
    }
}
