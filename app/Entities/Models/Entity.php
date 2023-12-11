<?php

namespace BookStack\Entities\Models;

use BookStack\Activity\Models\Activity;
use BookStack\Activity\Models\Comment;
use BookStack\Activity\Models\Favouritable;
use BookStack\Activity\Models\Favourite;
use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Models\Tag;
use BookStack\Activity\Models\View;
use BookStack\Activity\Models\Viewable;
use BookStack\Activity\Models\Watch;
use BookStack\App\Model;
use BookStack\App\Sluggable;
use BookStack\Entities\Tools\SlugGenerator;
use BookStack\Permissions\JointPermissionBuilder;
use BookStack\Permissions\Models\EntityPermission;
use BookStack\Permissions\Models\JointPermission;
use BookStack\Permissions\PermissionApplicator;
use BookStack\References\Reference;
use BookStack\Search\SearchIndex;
use BookStack\Search\SearchTerm;
use BookStack\Users\Models\HasCreatorAndUpdater;
use BookStack\Users\Models\HasOwner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Entity
 * The base class for book-like items such as pages, chapters & books.
 * This is not a database model in itself but extended.
 *
 * @property int        $id
 * @property string     $name
 * @property string     $slug
 * @property Carbon     $created_at
 * @property Carbon     $updated_at
 * @property Carbon     $deleted_at
 * @property int        $created_by
 * @property int        $updated_by
 * @property Collection $tags
 *
 * @method static Entity|Builder visible()
 * @method static Builder withLastView()
 * @method static Builder withViewCount()
 */
abstract class Entity extends Model implements Sluggable, Favouritable, Viewable, Deletable, Loggable
{
    use SoftDeletes;
    use HasCreatorAndUpdater;
    use HasOwner;

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
    public function scopeVisible(Builder $query): Builder
    {
        return app()->make(PermissionApplicator::class)->restrictEntityQuery($query);
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
     */
    public function matches(self $entity): bool
    {
        return [get_class($this), $this->id] === [get_class($entity), $entity->id];
    }

    /**
     * Checks if the current entity matches or contains the given.
     */
    public function matchesOrContains(self $entity): bool
    {
        if ($this->matches($entity)) {
            return true;
        }

        if (($entity instanceof BookChild) && $this instanceof Book) {
            return $entity->book_id === $this->id;
        }

        if ($entity instanceof Page && $this instanceof Chapter) {
            return $entity->chapter_id === $this->id;
        }

        return false;
    }

    /**
     * Gets the activity objects for this entity.
     */
    public function activity(): MorphMany
    {
        return $this->morphMany(Activity::class, 'entity')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get View objects for this entity.
     */
    public function views(): MorphMany
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /**
     * Get the Tag models that have been user assigned to this entity.
     */
    public function tags(): MorphMany
    {
        return $this->morphMany(Tag::class, 'entity')->orderBy('order', 'asc');
    }

    /**
     * Get the comments for an entity.
     */
    public function comments(bool $orderByCreated = true): MorphMany
    {
        $query = $this->morphMany(Comment::class, 'entity');

        return $orderByCreated ? $query->orderBy('created_at', 'asc') : $query;
    }

    /**
     * Get the related search terms.
     */
    public function searchTerms(): MorphMany
    {
        return $this->morphMany(SearchTerm::class, 'entity');
    }

    /**
     * Get this entities restrictions.
     */
    public function permissions(): MorphMany
    {
        return $this->morphMany(EntityPermission::class, 'entity');
    }

    /**
     * Check if this entity has a specific restriction set against it.
     */
    public function hasPermissions(): bool
    {
        return $this->permissions()->count() > 0;
    }

    /**
     * Get the entity jointPermissions this is connected to.
     */
    public function jointPermissions(): MorphMany
    {
        return $this->morphMany(JointPermission::class, 'entity');
    }

    /**
     * Get the related delete records for this entity.
     */
    public function deletions(): MorphMany
    {
        return $this->morphMany(Deletion::class, 'deletable');
    }

    /**
     * Get the references pointing from this entity to other items.
     */
    public function referencesFrom(): MorphMany
    {
        return $this->morphMany(Reference::class, 'from');
    }

    /**
     * Get the references pointing to this entity from other items.
     */
    public function referencesTo(): MorphMany
    {
        return $this->morphMany(Reference::class, 'to');
    }

    /**
     * Check if this instance or class is a certain type of entity.
     * Examples of $type are 'page', 'book', 'chapter'.
     *
     * @deprecated Use instanceof instead.
     */
    public static function isA(string $type): bool
    {
        return static::getType() === strtolower($type);
    }

    /**
     * Get the entity type as a simple lowercase word.
     */
    public static function getType(): string
    {
        $className = array_slice(explode('\\', static::class), -1, 1)[0];

        return strtolower($className);
    }

    /**
     * Gets a limited-length version of the entities name.
     */
    public function getShortName(int $length = 25): string
    {
        if (mb_strlen($this->name) <= $length) {
            return $this->name;
        }

        return mb_substr($this->name, 0, $length - 3) . '...';
    }

    /**
     * Get an excerpt of this entity's descriptive content to the specified length.
     */
    public function getExcerpt(int $length = 100): string
    {
        $text = $this->{$this->textField} ?? '';

        if (mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length - 3) . '...';
        }

        return trim($text);
    }

    /**
     * Get the url of this entity.
     */
    abstract public function getUrl(string $path = '/'): string;

    /**
     * Get the parent entity if existing.
     * This is the "static" parent and does not include dynamic
     * relations such as shelves to books.
     */
    public function getParent(): ?self
    {
        if ($this instanceof Page) {
            return $this->chapter_id ? $this->chapter()->withTrashed()->first() : $this->book()->withTrashed()->first();
        }
        if ($this instanceof Chapter) {
            return $this->book()->withTrashed()->first();
        }

        return null;
    }

    /**
     * Rebuild the permissions for this entity.
     */
    public function rebuildPermissions()
    {
        app()->make(JointPermissionBuilder::class)->rebuildForEntity(clone $this);
    }

    /**
     * Index the current entity for search.
     */
    public function indexForSearch()
    {
        app()->make(SearchIndex::class)->indexEntity(clone $this);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshSlug(): string
    {
        $this->slug = app()->make(SlugGenerator::class)->generate($this);

        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function favourites(): MorphMany
    {
        return $this->morphMany(Favourite::class, 'favouritable');
    }

    /**
     * Check if the entity is a favourite of the current user.
     */
    public function isFavourite(): bool
    {
        return $this->favourites()
            ->where('user_id', '=', user()->id)
            ->exists();
    }

    /**
     * Get the related watches for this entity.
     */
    public function watches(): MorphMany
    {
        return $this->morphMany(Watch::class, 'watchable');
    }

    /**
     * {@inheritdoc}
     */
    public function logDescriptor(): string
    {
        return "({$this->id}) {$this->name}";
    }
}
