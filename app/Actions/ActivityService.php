<?php namespace BookStack\Actions;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Book;
use BookStack\Entities\Entity;

class ActivityService
{
    protected $activity;
    protected $user;
    protected $permissionService;

    /**
     * ActivityService constructor.
     * @param Activity $activity
     * @param PermissionService $permissionService
     */
    public function __construct(Activity $activity, PermissionService $permissionService)
    {
        $this->activity = $activity;
        $this->permissionService = $permissionService;
        $this->user = user();
    }

    /**
     * Add activity data to database.
     * @param \BookStack\Entities\Entity $entity
     * @param string $activityKey
     * @param int $bookId
     */
    public function add(Entity $entity, string $activityKey, int $bookId = null)
    {
        $activity = $this->newActivityForUser($activityKey, $bookId);
        $entity->activity()->save($activity);
        $this->setNotification($activityKey);
    }

    /**
     * Adds a activity history with a message, without binding to a entity.
     * @param string $activityKey
     * @param string $message
     * @param int $bookId
     */
    public function addMessage(string $activityKey, string $message, int $bookId = null)
    {
        $this->newActivityForUser($activityKey, $bookId)->forceFill([
            'extra' => $message
        ])->save();

        $this->setNotification($activityKey);
    }

    /**
     * Get a new activity instance for the current user.
     * @param string $key
     * @param int|null $bookId
     * @return Activity
     */
    protected function newActivityForUser(string $key, int $bookId = null)
    {
        return $this->activity->newInstance()->forceFill([
            'key' => strtolower($key),
            'user_id' => $this->user->id,
            'book_id' => $bookId ?? 0,
        ]);
    }

    /**
     * Removes the entity attachment from each of its activities
     * and instead uses the 'extra' field with the entities name.
     * Used when an entity is deleted.
     * @param \BookStack\Entities\Entity $entity
     * @return mixed
     */
    public function removeEntity(Entity $entity)
    {
        // TODO - Rewrite to db query.
        $activities = $entity->activity;
        foreach ($activities as $activity) {
            $activity->extra = $entity->name;
            $activity->entity_id = 0;
            $activity->entity_type = null;
            $activity->save();
        }
        return $activities;
    }

    /**
     * Gets the latest activity.
     * @param int $count
     * @param int $page
     * @return array
     */
    public function latest($count = 20, $page = 0)
    {
        $activityList = $this->permissionService
            ->filterRestrictedEntityRelations($this->activity, 'activities', 'entity_id', 'entity_type')
            ->orderBy('created_at', 'desc')
            ->with('user', 'entity')
            ->skip($count * $page)
            ->take($count)
            ->get();

        return $this->filterSimilar($activityList);
    }

    /**
     * Gets the latest activity for an entity, Filtering out similar
     * items to prevent a message activity list.
     * @param \BookStack\Entities\Entity $entity
     * @param int $count
     * @param int $page
     * @return array
     */
    public function entityActivity($entity, $count = 20, $page = 1)
    {
        if ($entity->isA('book')) {
            $query = $this->activity->where('book_id', '=', $entity->id);
        } else {
            $query = $this->activity->where('entity_type', '=', $entity->getMorphClass())
                ->where('entity_id', '=', $entity->id);
        }
        
        $activity = $this->permissionService
            ->filterRestrictedEntityRelations($query, 'activities', 'entity_id', 'entity_type')
            ->orderBy('created_at', 'desc')
            ->with(['entity', 'user.avatar'])
            ->skip($count * ($page - 1))
            ->take($count)
            ->get();

        return $this->filterSimilar($activity);
    }

    /**
     * Get latest activity for a user, Filtering out similar
     * items.
     * @param $user
     * @param int $count
     * @param int $page
     * @return array
     */
    public function userActivity($user, $count = 20, $page = 0)
    {
        $activityList = $this->permissionService
            ->filterRestrictedEntityRelations($this->activity, 'activities', 'entity_id', 'entity_type')
            ->orderBy('created_at', 'desc')->where('user_id', '=', $user->id)->skip($count * $page)->take($count)->get();
        return $this->filterSimilar($activityList);
    }

    /**
     * Filters out similar activity.
     * @param Activity[] $activities
     * @return array
     */
    protected function filterSimilar($activities)
    {
        $newActivity = [];
        $previousItem = false;
        foreach ($activities as $activityItem) {
            if ($previousItem === false) {
                $previousItem = $activityItem;
                $newActivity[] = $activityItem;
                continue;
            }
            if (!$activityItem->isSimilarTo($previousItem)) {
                $newActivity[] = $activityItem;
            }
            $previousItem = $activityItem;
        }
        return $newActivity;
    }

    /**
     * Flashes a notification message to the session if an appropriate message is available.
     * @param $activityKey
     */
    protected function setNotification($activityKey)
    {
        $notificationTextKey = 'activities.' . $activityKey . '_notification';
        if (trans()->has($notificationTextKey)) {
            $message = trans($notificationTextKey);
            session()->flash('success', $message);
        }
    }

    /**
     * Log failed accesses, for further processing by tools like Fail2Ban
     *
     * @param username
     * @return void
      */
    public function logFailedAccess($username)
    {
        $log_msg = config('logging.failed_access_message');

        if (!is_string($username) || !is_string($log_msg) || strlen($log_msg)<1)
            return;

        $log_msg = str_replace("%u", $username, $log_msg);
        error_log($log_msg, 4);
    }
}
