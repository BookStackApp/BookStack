<?php

namespace BookStack\Uploads;

use BookStack\App\Model;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\Book;
use BookStack\Permissions\Models\JointPermission;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Users\Models\HasCreatorAndUpdater;
use BookStack\Users\Models\OwnableInterface;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int    $id
 * @property string $name
 * @property string $path
 * @property string $extension
 * @property ?Page  $page
 * @property ?Book  $book
 * @property ?Entity $attachable
 * @property string $attachable_type
 * @property int    $attachable_id
 * @property bool   $external
 * @property int    $uploaded_to
 * @property User   $updatedBy
 * @property User   $createdBy
 *
 * @method static Entity|Builder visible()
 */
class Attachment extends Model implements OwnableInterface
{
    use HasCreatorAndUpdater;
    use HasFactory;

    protected $fillable = ['name', 'order'];
    protected $hidden = ['path', 'page'];
    protected $casts = [
        'external' => 'bool',
    ];

    /**
     * Get the downloadable file name for this upload.
     */
    public function getFileName(): string
    {
        if (str_contains($this->name, '.')) {
            return $this->name;
        }

        return $this->name . '.' . $this->extension;
    }

    /**
     * Get the polymorphic entity this file was uploaded to (page or book).
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo('attachable');
    }

    /**
     * Get the page this file was uploaded to (backward compatibility).
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'uploaded_to');
    }

    /**
     * Get the book this file was uploaded to.
     * Returns null if the attachment is not attached to a book.
     */
    public function book(): ?Book
    {
        if ($this->attachable_type === 'BookStack\\Entities\\Models\\Book') {
            return $this->attachable;
        }
        return null;
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'attachable_id')
            ->where(function($query) {
                $query->where('joint_permissions.entity_type', '=', 'page')
                      ->orWhere('joint_permissions.entity_type', '=', 'book');
            });
    }

    /**
     * Get the url of this file.
     */
    public function getUrl($openInline = false): string
    {
        if ($this->external && !str_starts_with($this->path, 'http')) {
            return $this->path;
        }

        return url('/attachments/' . $this->id . ($openInline ? '?open=true' : ''));
    }

    /**
     * Get the representation of this attachment in a format suitable for the page editors.
     * Detects and adapts video content to use an inline video embed.
     */
    public function editorContent(): array
    {
        $videoExtensions = ['mp4', 'webm', 'mkv', 'ogg', 'avi'];
        if (in_array(strtolower($this->extension), $videoExtensions)) {
            $html = '<video src="' . e($this->getUrl(true)) . '" controls width="480" height="270"></video>';
            return ['text/html' => $html, 'text/plain' => $html];
        }

        return ['text/html' => $this->htmlLink(), 'text/plain' => $this->markdownLink()];
    }

    /**
     * Generate the HTML link to this attachment.
     */
    public function htmlLink(): string
    {
        return '<a target="_blank" href="' . e($this->getUrl()) . '">' . e($this->name) . '</a>';
    }

    /**
     * Generate a MarkDown link to this attachment.
     */
    public function markdownLink(): string
    {
        return '[' . $this->name . '](' . $this->getUrl() . ')';
    }

    /**
     * Scope the query to those attachments that are visible based upon related entity permissions.
     */
    public function scopeVisible(): Builder
    {
        $permissions = app()->make(PermissionApplicator::class);

        // Use polymorphic relationship restriction for both pages and books
        return $permissions->restrictEntityRelationQuery(
            self::query(),
            'attachments',
            'attachable_id',
            'attachable_type'
        );
    }
}
