<?php

namespace BookStack\Entities\Models;

use BookStack\Model;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingProgress extends Model
{
    protected $table = 'reading_progress';

    protected $fillable = [
        'user_id',
        'page_id',
        'progress_percentage',
        'scroll_position',
        'time_spent_seconds',
        'is_completed',
        'last_read_at',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'scroll_position' => 'integer',
        'time_spent_seconds' => 'integer',
        'is_completed' => 'boolean',
        'last_read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public static function forUserAndPage(int $userId, int $pageId): ?self
    {
        return static::where('user_id', $userId)
            ->where('page_id', $pageId)
            ->first();
    }

    public static function updateOrCreateProgress(int $userId, int $pageId, array $data): self
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'page_id' => $pageId],
            array_merge($data, ['last_read_at' => now()])
        );
    }

    public static function getUserReadingStats(int $userId): array
    {
        $stats = static::where('user_id', $userId)
            ->selectRaw('COUNT(*) as total_pages_read')
            ->selectRaw('SUM(time_spent_seconds) as total_time_spent')
            ->selectRaw('AVG(time_spent_seconds) as average_time_per_page')
            ->selectRaw('SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_pages')
            ->first();

        return [
            'total_pages_read' => $stats->total_pages_read ?? 0,
            'total_time_spent' => $stats->total_time_spent ?? 0,
            'average_time_per_page' => $stats->average_time_per_page ?? 0,
            'completed_pages' => $stats->completed_pages ?? 0,
        ];
    }
}