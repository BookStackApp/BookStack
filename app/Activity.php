<?php

namespace Oxbow;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string  key
 * @property \User   user
 * @property \Entity entity
 * @property string  extra
 */
class Activity extends Model
{

    /**
     * Get the entity for this activity.
     * @return bool
     */
    public function entity()
    {
        if ($this->entity_id) {
            return $this->morphTo('entity')->first();
        } else {
            return false;
        }
    }

    /**
     * Get the user this activity relates to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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

    /**
     * Checks if another Activity matches the general information of another.
     * @param $activityB
     * @return bool
     */
    public function isSimilarTo($activityB) {
        return [$this->key, $this->entitiy_type, $this->entitiy_id] === [$activityB->key, $activityB->entitiy_type, $activityB->entitiy_id];
    }

}
