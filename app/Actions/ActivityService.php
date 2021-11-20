<?php

namespace BookStack\Actions;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Interfaces\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Log;

class ActivityService
{
    protected $activity;
    protected $permissionService;

    public function __construct(Activity $activity, PermissionService $permissionService)
    {
        $this->activity = $activity;
        $this->permissionService = $permissionService;
    }

    /**
     * Add activity data to database for an entity.
     */
    public function addForEntity(Entity $entity, string $type)
    {
        $activity = $this->newActivityForUser($type);
        $entity->activity()->save($activity);
        $this->setNotification($type);
    }

    /**
     * Add a generic activity event to the database.
     *
     * @param string|Loggable $detail
     */
    public function add(string $type, $detail = '')
    {
        if ($detail instanceof Loggable) {
            $detail = $detail->logDescriptor();
        }

        $activity = $this->newActivityForUser($type);
        $activity->detail = $detail;
        $activity->save();
        $this->setNotification($type);
    }

    /**
     * Get a new activity instance for the current user.
     */
    protected function newActivityForUser(string $type): Activity
    {
        $ip = request()->ip() ?? '';

        return $this->activity->newInstance()->forceFill([
            'type'     => strtolower($type),
            'user_id'  => user()->id,
            'ip'       => config('app.env') === 'demo' ? '127.0.0.1' : $ip,
        ]);
    }

    /**
     * Removes the entity attachment from each of its activities
     * and instead uses the 'extra' field with the entities name.
     * Used when an entity is deleted.
     */
    public function removeEntity(Entity $entity)
    {
        $entity->activity()->update([
            'detail'       => $entity->name,
            'entity_id'    => null,
            'entity_type'  => null,
        ]);
    }

    /**
     * Gets the latest activity.
     */
    public function latest(int $count = 20, int $page = 0): array
    {
        $activityList = $this->permissionService
            ->filterRestrictedEntityRelations($this->activity->newQuery(), 'activities', 'entity_id', 'entity_type')
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
        /** @var array<string, int[]> $queryIds */
        $queryIds = [$entity->getMorphClass() => [$entity->id]];

        if ($entity instanceof Book) {
            $queryIds[(new Chapter())->getMorphClass()] = $entity->chapters()->visible()->pluck('id');
        }
        if ($entity instanceof Book || $entity instanceof Chapter) {
            $queryIds[(new Page())->getMorphClass()] = $entity->pages()->visible()->pluck('id');
        }

        $query = $this->activity->newQuery();
        $query->where(function (Builder $query) use ($queryIds) {
            foreach ($queryIds as $morphClass => $idArr) {
                $query->orWhere(function (Builder $innerQuery) use ($morphClass, $idArr) {
                    $innerQuery->where('entity_type', '=', $morphClass)
                        ->whereIn('entity_id', $idArr);
                });
            }
        });

        $activity = $query->orderBy('created_at', 'desc')
            ->with(['entity' => function (Relation $query) {
                $query->withTrashed();
            }, 'user.avatar'])
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
            ->filterRestrictedEntityRelations($this->activity->newQuery(), 'activities', 'entity_id', 'entity_type')
            ->orderBy('created_at', 'desc')
            ->where('user_id', '=', $user->id)
            ->skip($count * $page)
            ->take($count)
            ->get();

        return $this->filterSimilar($activityList);
    }

    /**
     * Filters out similar activity.
     *
     * @param Activity[] $activities
     *
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
    protected function setNotification(string $type)
    {
        $notificationTextKey = 'activities.' . $type . '_notification';
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

        $message = str_replace('%u', $username, $message);
        $channel = config('logging.failed_login.channel');
        Log::channel($channel)->warning($message);
    }
}
