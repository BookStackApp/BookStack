<?php

namespace BookStack\Exports;

use BookStack\Activity\Models\Loggable;
use BookStack\Exports\ZipExports\Models\ZipExportBook;
use BookStack\Exports\ZipExports\Models\ZipExportChapter;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $path
 * @property string $name
 * @property int $size - ZIP size in bytes
 * @property string $type
 * @property string $metadata
 * @property int $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $createdBy
 */
class Import extends Model implements Loggable
{
    use HasFactory;

    protected $hidden = ['metadata'];

    public function getSizeString(): string
    {
        $mb = round($this->size / 1000000, 2);
        return "{$mb} MB";
    }

    /**
     * Get the URL to view/continue this import.
     */
    public function getUrl(string $path = ''): string
    {
        $path = ltrim($path, '/');
        return url("/import/{$this->id}" . ($path ? '/' . $path : ''));
    }

    public function logDescriptor(): string
    {
        return "({$this->id}) {$this->name}";
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function decodeMetadata(): ZipExportBook|ZipExportChapter|ZipExportPage|null
    {
        $metadataArray = json_decode($this->metadata, true);
        return match ($this->type) {
            'book' => ZipExportBook::fromArray($metadataArray),
            'chapter' => ZipExportChapter::fromArray($metadataArray),
            'page' => ZipExportPage::fromArray($metadataArray),
            default => null,
        };
    }
}
