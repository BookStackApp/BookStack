<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'page_id',
        'progress_percentage',
        'last_read_position',
        'started_reading_at',
        'last_read_at',
        'total_read_time_seconds',
        'is_completed',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
        'last_read_position' => 'integer',
        'started_reading_at' => 'datetime',
        'last_read_at' => 'datetime',
        'total_read_time_seconds' => 'integer',
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}