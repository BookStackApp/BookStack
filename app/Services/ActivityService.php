<?php namespace Oxbow\Services;

use Illuminate\Support\Facades\Auth;
use Oxbow\Activity;
use Oxbow\Entity;

class ActivityService
{
    protected $activity;
    protected $user;

    /**
     * ActivityService constructor.
     * @param $activity
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
        $this->user = Auth::user();
    }


    /**
     * Add activity data to database.
     * @para Entity $entity
     * @param $activityKey
     * @param int $bookId
     */
    public function add(Entity $entity, $activityKey, $bookId = 0, $extra = false)
    {
        $this->activity->user_id = $this->user->id;
        $this->activity->book_id = $bookId;
        $this->activity->key = strtolower($activityKey);
        if($extra !== false) {
            $this->activity->extra = $extra;
        }
        $entity->activity()->save($this->activity);
    }

    /**
     * Adds a activity history with a message & without binding to a entitiy.
     * @param $activityKey
     * @param int $bookId
     * @param bool|false $extra
     */
    public function addMessage($activityKey, $bookId = 0, $extra = false)
    {
        $this->activity->user_id = $this->user->id;
        $this->activity->book_id = $bookId;
        $this->activity->key = strtolower($activityKey);
        if($extra !== false) {
            $this->activity->extra = $extra;
        }
        $this->activity->save();
    }

}