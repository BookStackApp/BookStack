<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\PageContent;
use BookStack\Entities\Tools\PageEditorType;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Uploads\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Page.
 * @property EntityPageContents $pageData
 * @property int          $chapter_id
 * @property string       $html
 * @property string       $markdown
 * @property string       $text
 * @property bool         $template
 * @property bool         $draft
 * @property int          $revision_count
 * @property string       $editor
 * @property Chapter      $chapter
 * @property Collection   $attachments
 * @property Collection   $revisions
 * @property PageRevision $currentRevision
 */
class Page extends BookChild
{
    use HasFactory;

    public string $textField = 'text';
    public string $htmlField = 'html';

    protected $hidden = ['html', 'markdown', 'text', 'pivot', 'deleted_at'];

    protected $casts = [
        'draft'    => 'boolean',
        'template' => 'boolean',
    ];

    /**
     * Get the entities that are visible to the current user.
     */
    public function scopeVisible(Builder $query): Builder
    {
        $query = app()->make(PermissionApplicator::class)->restrictDraftsOnPageQuery($query);

        return parent::scopeVisible($query);
    }

    /**
     * Get the page-specific data for this page.
     */
    public function pageData(): HasOne
    {
        return $this->hasOne(EntityPageContents::class, 'page_id', 'id');
    }

    /**
     * Get the chapter that this page is in, If applicable.
     */
    public function chapter(): BelongsTo
    {
        return $this->pageData->belongsTo(Chapter::class);
    }

    /**
     * Check if this page has a chapter.
     */
    public function hasChapter(): bool
    {
        return $this->chapter()->count() > 0;
    }

    /**
     * Get the associated page revisions, ordered by created date.
     * Only provides actual saved page revision instances, Not drafts.
     */
    public function revisions(): HasMany
    {
        return $this->allRevisions()
            ->where('type', '=', 'version')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
    }

    /**
     * Get the current revision for the page if existing.
     */
    public function currentRevision(): HasOne
    {
        return $this->hasOne(PageRevision::class)
            ->where('type', '=', 'version')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
    }

    /**
     * Get all revision instances assigned to this page.
     * Includes all types of revisions.
     */
    public function allRevisions(): HasMany
    {
        return $this->hasMany(PageRevision::class);
    }

    /**
     * Get the attachments assigned to this page.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'uploaded_to')->orderBy('order', 'asc');
    }

    /**
     * Get the url of this page.
     */
    public function getUrl(string $path = ''): string
    {
        $parts = [
            'books',
            urlencode($this->book_slug ?? $this->book->slug),
            $this->pageData->draft ? 'draft' : 'page',
            $this->pageData->draft ? $this->id : urlencode($this->slug),
            trim($path, '/'),
        ];

        return url('/' . implode('/', $parts));
    }

    /**
     * Get this page for JSON display.
     */
    public function forJsonDisplay(): self
    {
        $refreshed = $this->refresh()->unsetRelations()->load(['tags', 'createdBy', 'updatedBy', 'ownedBy']);
        $refreshed->setHidden(array_diff($refreshed->getHidden(), ['pageData']));
        $refreshed->setAttribute('raw_html', $refreshed->pageData->html);
        $refreshed->setAttribute('html', (new PageContent($refreshed))->render());

        return $refreshed;
    }

    /**
     * @return HasOne<EntityPageContents, $this>
     */
    public function relatedData(): HasOne
    {
        return $this->hasOne(EntityPageContents::class, 'page_id', 'id');
    }
}
