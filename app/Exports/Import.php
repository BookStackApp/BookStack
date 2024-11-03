<?php

namespace BookStack\Exports;

use BookStack\Activity\Models\Loggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $path
 * @property string $name
 * @property int $size - ZIP size in bytes
 * @property int $book_count
 * @property int $chapter_count
 * @property int $page_count
 * @property int $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Import extends Model implements Loggable
{
    use HasFactory;

    public const TYPE_BOOK = 'book';
    public const TYPE_CHAPTER = 'chapter';
    public const TYPE_PAGE = 'page';

    /**
     * Get the type (model) that this import is intended to be.
     */
    public function getType(): string
    {
        if ($this->book_count === 1) {
            return self::TYPE_BOOK;
        } elseif ($this->chapter_count === 1) {
            return self::TYPE_CHAPTER;
        }

        return self::TYPE_PAGE;
    }

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
}
