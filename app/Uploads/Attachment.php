<?php

namespace BookStack\Uploads;

use BookStack\App\Model;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Permissions\Models\JointPermission;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Users\Models\HasCreatorAndUpdater;
use BookStack\Users\Models\OwnableInterface;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int    $id
 * @property string $name
 * @property string $path
 * @property string $extension
 * @property ?Page  $page
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

    public const UPLOAD_TO_PAGE = 'page';
    public const UPLOAD_TO_CHAPTER = 'chapter';
    public const UPLOAD_TO_BOOK = 'book';

    public const UPLOAD_TO_ENTITY_TYPES = [
        self::UPLOAD_TO_PAGE,
        self::UPLOAD_TO_CHAPTER,
        self::UPLOAD_TO_BOOK,
    ];

    protected $fillable = ['name', 'order'];
    protected $hidden = ['path', 'page', 'uploadedTo'];
    protected $casts = [
        'external' => 'bool',
    ];

    /**
     * Ensure uploaded target type defaults to page for backward compatibility.
     */
    public function getUploadedToTypeAttribute(?string $value): string
    {
        return $value ?: self::UPLOAD_TO_PAGE;
    }

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
     * Get the page this file was uploaded to.
     */
    public function page(): BelongsTo
    {
        $relation = $this->belongsTo(Page::class, 'uploaded_to');

        if ($this->uploaded_to_type !== self::UPLOAD_TO_PAGE) {
            $relation->whereRaw('1 = 0');
        }

        return $relation;
    }

    /**
     * Get the entity this attachment is uploaded to.
     */
    public function uploadedTo(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'uploaded_to_type', 'uploaded_to');
    }

    public function jointPermissions(): HasMany
    {
        return $this->hasMany(JointPermission::class, 'entity_id', 'uploaded_to')
            ->where('joint_permissions.entity_type', '=', $this->uploaded_to_type);
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
     * Scope the query to those attachments that are visible based upon related page permissions.
     */
    public function scopeVisible(): Builder
    {
        $permissions = app()->make(PermissionApplicator::class);

        return $permissions
            ->restrictEntityRelationQuery(self::query(), 'attachments', 'uploaded_to', 'uploaded_to_type')
            ->whereIn('uploaded_to_type', self::UPLOAD_TO_ENTITY_TYPES);
    }

    /**
     * Get the target entity class for an upload type.
     */
    public static function classForUploadType(string $type): ?string
    {
        return match ($type) {
            self::UPLOAD_TO_PAGE => Page::class,
            self::UPLOAD_TO_CHAPTER => Chapter::class,
            self::UPLOAD_TO_BOOK => Book::class,
            default => null,
        };
    }
}
