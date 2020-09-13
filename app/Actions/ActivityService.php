<?php namespace BookStack\Actions;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\User;
use BookStack\Entities\Entity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ActivityService
{
    protected $activity;
    protected $user;
    protected $permissionService;

    /**
     * ActivityService constructor.
     */
    public function __construct(Activity $activity, PermissionService $permissionService)
    {
        $this->activity = $activity;
        $this->permissionService = $permissionService;
        $this->user = user();
    }

    /**
     * Add activity data to database.
     */
    public function add(Entity $entity, string $activityKey, ?int $bookId = null)
    {
        $activity = $this->newActivityForUser($activityKey, $bookId);
        $entity->activity()->save($activity);
        $this->setNotification($activityKey);
    }

    /**
     * Adds a activity history with a message, without binding to a entity.
     */
    public function addMessage(string $activityKey, string $message, ?int $bookId = null)
    {
        $this->newActivityForUser($activityKey, $bookId)->forceFill([
            'extra' => $message
        ])->save();

        $this->setNotification($activityKey);
    }

    /**
     * Get a new activity instance for the current user.
     */
    protected function newActivityForUser(string $key, ?int $bookId = null): Activity
    {
        return $this->activity->newInstance()->forceFill([
            'key'     => strtolower($key),
            'user_id' => $this->user->id,
            'book_id' => $bookId ?? 0,
        ]);
    }

    /**
     * Removes the entity attachment from each of its activities
     * and instead uses the 'extra' field with the entities name.
     * Used when an entity is deleted.
     */
    public function removeEntity(Entity $entity): Collection
    {
        $activities = $entity->activity()->get();
        $entity->activity()->update([
            'extra'       => $entity->name,
            'entity_id'   => 0,
            'entity_type' => '',
        ]);
        return $activities;
    }

    /**
     * Gets the latest activity.
     */
    public function latest(int $count = 20, int $page = 0): array
    {
        $activityList = $this->permissionService
            ->filterRestrictedEntityRelations($this->activity, 'activities', 'entity_id', 'entity_type')
            ->orderBy('created_at', 'desc')
            ->with(['user', 'entity'])
            ->skip($count * $page)
            ->take($count)
            ->get();

        return $this->filterSimilar($activityList);
    }

    /**
     * Gets the latest activity for an entity, Filtering out similar
     * items to prevent a message activity list.
     */
    public function entityActivity(Entity $entity, int $count = 20, int $page = 1): array
    {
        if ($entity->isA('book')) {
            $query = $this->activity->newQuery()->where('book_id', '=', $entity->id);
        } else {
            $query = $this->activity->newQuery()->where('entity_type', '=', $entity->getMorphClass())
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
     * Get latest activity for a user, Filtering out similar items.
     */
    public function userActivity(User $user, int $count = 20, int $page = 0): array
    {
        $activityList = $this->permissionService
            ->filterRestrictedEntityRelations($this->activity, 'activities', 'entity_id', 'entity_type')
            ->orderBy('created_at', 'desc')
            ->where('user_id', '=', $user->id)
            ->skip($count * $page)
            ->take($count)
            ->get();

        return $this->filterSimilar($activityList);
    }

    /**
     * Filters out similar activity.
     * @param Activity[] $activities
     * @return array
     */
    protected function filterSimilar(iterable $activities): array
    {
        $newActivity = [];
        $previousItem = null;

        foreach ($activities as $activityItem) {
            if (!$previousItem || !$activityItem->isSimilarTo($previousItem)) {
                $newActivity[] = $activityItem;
            }

            $previousItem = $activityItem;
        }

        return $newActivity;
    }

    /**
     * Flashes a notification message to the session if an appropriate message is available.
     */
    protected function setNotification(string $activityKey)
    {
        $notificationTextKey = 'activities.' . $activityKey . '_notification';
        if (trans()->has($notificationTextKey)) {
            $message = trans($notificationTextKey);
            session()->flash('success', $message);
        }
    }

    /**
     * Log out a failed login attempt, Providing the given username
     * as part of the message if the '%u' string is used.
     */
    public function logFailedLogin(string $username)
    {
        $message = config('logging.failed_login.message');
        if (!$message) {
            return;
        }

        $message = str_replace("%u", $username, $message);
        $channel = config('logging.failed_login.channel');
        Log::channel($channel)->warning($message);
    }
}
