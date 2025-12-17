<?php

namespace BookStack\Activity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $mentionable_type
 * @property int $mentionable_id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MentionHistory extends Model
{
    protected $table = 'mention_history';
}
