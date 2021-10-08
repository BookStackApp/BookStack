<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;
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

        $userMessage = $count > 1 ? trans('entities.pages_draft_edit_active.start_a', ['count' => $count]) : trans('entities.pages_draft_edit_active.start_b', ['userName' => $pageDraftEdits->first()->createdBy->name]);
        $timeMessage = trans('entities.pages_draft_edit_active.time_b', ['minCount'=> 60]);

        return trans('entities.pages_draft_edit_active.message', ['start' => $userMessage, 'time' => $timeMessage]);
    }

    /**
     * Get any editor clash warning messages to show for the given draft revision.
     *
     * @param PageRevision|Page $draft
     *
     * @return string[]
     */
    public function getWarningMessagesForDraft($draft): array
    {
        $warnings = [];

        if ($this->hasActiveEditing()) {
            $warnings[] = $this->activeEditingMessage();
        }

        if ($draft instanceof PageRevision && $this->hasPageBeenUpdatedSinceDraftCreated($draft)) {
            $warnings[] = trans('entities.pages_draft_page_changed_since_creation');
        }

        return $warnings;
    }

    /**
     * Check if the page has been updated since the draft has been saved.
     */
    protected function hasPageBeenUpdatedSinceDraftCreated(PageRevision $draft): bool
    {
        return $draft->page->updated_at->timestamp > $draft->created_at->timestamp;
    }

    /**
     * Get the message to show when the user will be editing one of their drafts.
     */
    public function getEditingActiveDraftMessage(PageRevision $draft): string
    {
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
