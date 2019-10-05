<?php namespace BookStack\Entities;

use BookStack\Actions\Activity;
use BookStack\Actions\Comment;
use BookStack\Actions\Tag;
use BookStack\Actions\View;
use BookStack\Auth\Permissions\EntityPermission;
use BookStack\Auth\Permissions\JointPermission;
use BookStack\Facades\Permissions;
use BookStack\Ownable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class Entity
 * The base class for book-like items such as pages, chapters & books.
 * This is not a database model in itself but extended.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property boolean $restricted
 * @property Collection $tags
 * @method static Entity|Builder visible()
 * @method static Entity|Builder hasPermission(string $permission)
 * @method static Builder withLastView()
 * @method static Builder withViewCount()
 *
 * @package BookStack\Entities
 */
class Entity extends Ownable
{

    /**
     * @var string - Name of property where the main text content is found
     */
    public $textField = 'description';

    /**
     * @var float - Multiplier for search indexing.
     */
    public $searchFactor = 1.0;

    /**
     * Get the entities that are visible to the current user.
     */
    public function scopeVisible(Builder $query)
    {
        return $this->scopeHasPermission($query, 'view');
    }

    /**
     * Scope the query to those entities that the current user has the given permission for.
     */
    public function scopeHasPermission(Builder $query, string $permission)
    {
        return Permissions::restrictEntityQuery($query, $permission);
    }

    /**
     * Query scope to get the last view from the current user.
     */
    public function scopeWithLastView(Builder $query)
    {
        $viewedAtQuery = View::query()->select('updated_at')
            ->whereColumn('viewable_id', '=', $this->getTable() . '.id')
            ->where('viewable_type', '=', $this->getMorphClass())
            ->where('user_id', '=', user()->id)
            ->take(1);

        return $query->addSelect(['last_viewed_at' => $viewedAtQuery]);
    }

    /**
     * Query scope to get the total view count of the entities.
     */
    public function scopeWithViewCount(Builder $query)
    {
        $viewCountQuery = View::query()->selectRaw('SUM(views) as view_count')
            ->whereColumn('viewable_id', '=', $this->getTable() . '.id')
            ->where('viewable_type', '=', $this->getMorphClass())->take(1);

        $query->addSelect(['view_count' => $viewCountQuery]);
    }

    /**
     * Compares this entity to another given entity.
     * Matches by comparing class and id.
     * @param $entity
     * @return bool
     */
    public function matches($entity)
    {
        return [get_class($this), $this->id] === [get_class($entity), $entity->id];
    }

    /**
     * Checks if an entity matches or contains another given entity.
     * @param Entity $entity
     * @return bool
     */
    public function matchesOrContains(Entity $entity)
    {
        $matches = [get_class($this), $this->id] === [get_class($entity), $entity->id];

        if ($matches) {
            return true;
        }

        if (($entity->isA('chapter') || $entity->isA('page')) && $this->isA('book')) {
            return $entity->book_id === $this->id;
        }

        if ($entity->isA('page') && $this->isA('chapter')) {
            return $entity->chapter_id === $this->id;
        }

        return false;
    }

    /**
     * Gets the activity objects for this entity.
     * @return MorphMany
     */
    public function activity()
    {
        return $this->morphMany(Activity::class, 'entity')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get View objects for this entity.
     */
    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /**
     * Get the Tag models that have been user assigned to this entity.
     * @return MorphMany
     */
    public function tags()
    {
        return $this->morphMany(Tag::class, 'entity')->orderBy('order', 'asc');
    }

    /**
     * Get the comments for an entity
     * @param bool $orderByCreated
     * @return MorphMany
     */
    public function comments($orderByCreated = true)
    {
        $query = $this->morphMany(Comment::class, 'entity');
        return $orderByCreated ? $query->orderBy('created_at', 'asc') : $query;
    }

    /**
     * Get the related search terms.
     * @return MorphMany
     */
    public function searchTerms()
    {
        return $this->morphMany(SearchTerm::class, 'entity');
    }

    /**
     * Get this entities restrictions.
     */
    public function permissions()
    {
        return $this->morphMany(EntityPermission::class, 'restrictable');
    }

    /**
     * Check if this entity has a specific restriction set against it.
     * @param $role_id
     * @param $action
     * @return bool
     */
    public function hasRestriction($role_id, $action)
    {
        return $this->permissions()->where('role_id', '=', $role_id)
            ->where('action', '=', $action)->count() > 0;
    }

    /**
     * Get the entity jointPermissions this is connected to.
     * @return MorphMany
     */
    public function jointPermissions()
    {
        return $this->morphMany(JointPermission::class, 'entity');
    }

    /**
     * Allows checking of the exact class, Used to check entity type.
     * Cleaner method for is_a.
     * @param $type
     * @return bool
     */
    public static function isA($type)
    {
        return static::getType() === strtolower($type);
    }

    /**
     * Get entity type.
     * @return mixed
     */
    public static function getType()
    {
        return strtolower(static::getClassName());
    }

    /**
     * Get an instance of an entity of the given type.
     * @param $type
     * @return Entity
     */
    public static function getEntityInstance($type)
    {
        $types = ['Page', 'Book', 'Chapter', 'Bookshelf'];
        $className = str_replace([' ', '-', '_'], '', ucwords($type));
        if (!in_array($className, $types)) {
            return null;
        }

        return app('BookStack\\Entities\\' . $className);
    }

    /**
     * Gets a limited-length version of the entities name.
     * @param int $length
     * @return string
     */
    public function getShortName($length = 25)
    {
        if (mb_strlen($this->name) <= $length) {
            return $this->name;
        }
        return mb_substr($this->name, 0, $length - 3) . '...';
    }

    /**
     * Get the body text of this entity.
     * @return mixed
     */
    public function getText()
    {
        return $this->{$this->textField};
    }

    /**
     * Get an excerpt of this entity's descriptive content to the specified length.
     * @param int $length
     * @return mixed
     */
    public function getExcerpt(int $length = 100)
    {
        $text = $this->getText();
        if (mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length-3) . '...';
        }
        return trim($text);
    }

    /**
     * Get the url of this entity
     * @param $path
     * @return string
     */
    public function getUrl($path = '/')
    {
        return $path;
    }

    /**
     * Rebuild the permissions for this entity.
     */
    public function rebuildPermissions()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        Permissions::buildJointPermissionsForEntity($this);
    }

    /**
     * Index the current entity for search
     */
    public function indexForSearch()
    {
        $searchService = app()->make(SearchService::class);
        $searchService->indexEntity($this);
    }

    /**
     * Generate and set a new URL slug for this model.
     */
    public function refreshSlug(): string
    {
        $generator = new SlugGenerator($this);
        $this->slug = $generator->generate();
        return $this->slug;
    }
}
