<?php

namespace BookStack\Actions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $webhook_id
 * @property string $event
 */
class WebhookTrackedEvent extends Model
{
    protected $fillable = ['event'];

    use HasFactory;
}
