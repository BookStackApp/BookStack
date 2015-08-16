<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string key
 * @property \User user
 * @property \Entity entity
 * @property string extra
 */
class Activity extends Model
{
    public function entity()
    {
        if($this->entity_id) {
            return $this->morphTo('entity')->first();
        } else {
            return false;
        }
    }

    public function user()
    {
        return $this->belongsTo('Oxbow\User');
    }

    /**
     * Returns text from the language files, Looks up by using the
     * activity key.
     */
    public function getText()
    {
        return trans('activities.' . $this->key);
    }

}
