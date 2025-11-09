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
use BookStack\App\SluggableInterface;
use BookStack\Entities\Tools\SlugGenerator;
use BookStack\Permissions\JointPermissionBuilder;
use BookStack\Permissions\Models\EntityPermission;
use BookStack\Permissions\Models\JointPermission;
use BookStack\Permissions\PermissionApplicator;
use BookStack\References\Reference;
use BookStack\Search\SearchIndex;
use BookStack\Search\SearchTerm;
use BookStack\Users\Models\HasCreatorAndUpdater;
use BookStack\Users\Models\OwnableInterface;
use BookStack\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Entity
 * The base class for book-like items such as pages, chapters and books.
 * This is not a database model in itself but extended.
 *
 * @property int        $id
 * @property string     $type
 * @property string     $name
 * @property string     $slug
 * @property Carbon     $created_at
 * @property Carbon     $updated_at
 * @property Carbon     $deleted_at
 * @property int|null   $created_by
 * @property int|null   $updated_by
 * @property int|null   $owned_by
 * @property Collection $tags
 *
 * @method static Entity|Builder visible()
 * @method static Builder withLastView()
 * @method static Builder withViewCount()
 */
abstract class Entity extends Model implements
    SluggableInterface,
    Favouritable,
    Viewable,
    DeletableInterface,
    OwnableInterface,
    Loggable
{
    use SoftDeletes;
    use HasCreatorAndUpdater;

    /**
     * @var string - Name of property where the main text content is found
     */
    public string $textField = 'description';

    /**
     * @var string - Name of the property where the main HTML content is found
     */
    public string $htmlField = 'description_html';

    /**
     * @var float - Multiplier for search indexing.
     */
    public float $searchFactor = 1.0;

    /**
     * Set the table to be that used by all entities.
     */
    protected $table = 'entities';

    /**
     * Set a custom query builder for entities.
     */
    protected static string $builder = EntityQueryBuilder::class;

    public static array $commonFields = [
        'id',
        'type',
        'name',
        'slug',
        'book_id',
        'chapter_id',
        'priority',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'owned_by',
    ];

    /**
     * Override the save method to also save the contents for convenience.
     */
    public function save(array $options = []): bool
    {
        /** @var EntityPageData|EntityContainerData $contents */
        $contents = $this->relatedData()->firstOrNew();
        $contentFields = $this->getContentsAttributes();

        foreach ($contentFields as $key => $value) {
            $contents->setAttribute($key, $value);
            unset($this->attributes[$key]);
        }

        $this->setAttribute('type', $this->getMorphClass());
        $result = parent::save($options);
        $contentsResult = true;

        if ($result && $contents->isDirty()) {
            $contentsFillData = $contents instanceof EntityPageData ? ['page_id' => $this->id] : ['entity_id' => $this->id, 'entity_type' => $this->getMorphClass()];
            $contents->forceFill($contentsFillData);
            $contentsResult = $contents->save();
            $this->touch();
        }

        $this->forceFill($contentFields);

        return $result && $contentsResult;
    }

    /**
     * Check if this item is a container item.
     */
    public function isContainer(): bool
    {
        return $this instanceof Bookshelf ||
            $this instanceof Book ||
            $this instanceof Chapter;
    }

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
            ->whereColumn('viewable_id', '=', 'entities.id')
            ->whereColumn('viewable_type', '=', 'entities.type')
            ->where('user_id', '=', user()->id)
            ->take(1);

        return $query->addSelect(['last_viewed_at' => $viewedAtQuery]);
    }

    /**
     * Query scope to get the total view count of the entities.
     */
    public function scopeWithViewCount(Builder $query): void
    {
        $viewCountQuery = View::query()->selectRaw('SUM(views) as view_count')
            ->whereColumn('viewable_id', '=', 'entities.id')
            ->whereColumn('viewable_type', '=', 'entities.type')
            ->take(1);

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
        return $this->morphMany(Activity::class, 'loggable')
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
        return $this->morphMany(Tag::class, 'entity')
            ->orderBy('order', 'asc');
    }

    /**
     * Get the comments for an entity.
     * @return MorphMany<Comment, $this>
     */
    public function comments(bool $orderByCreated = true): MorphMany
    {
        $query = $this->morphMany(Comment::class, 'commentable');

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
     * Get this entities assigned permissions.
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
     * Get the user who owns this entity.
     * @return BelongsTo<User, $this>
     */
    public function ownedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owned_by');
    }

    public function getOwnerFieldName(): string
    {
        return 'owned_by';
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
     * Gets a limited-length version of the entity name.
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
            /** @var BelongsTo<Chapter|Book, Page>  $builder */
            $builder = $this->chapter_id ? $this->chapter() : $this->book();
            return $builder->withTrashed()->first();
        }
        if ($this instanceof Chapter) {
            /** @var BelongsTo<Book, Page>  $builder */
            $builder = $this->book();
            return $builder->withTrashed()->first();
        }

        return null;
    }

    /**
     * Rebuild the permissions for this entity.
     */
    public function rebuildPermissions(): void
    {
        app()->make(JointPermissionBuilder::class)->rebuildForEntity(clone $this);
    }

    /**
     * Index the current entity for search.
     */
    public function indexForSearch(): void
    {
        app()->make(SearchIndex::class)->indexEntity(clone $this);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshSlug(): string
    {
        $this->slug = app()->make(SlugGenerator::class)->generate($this, $this->name);

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

    /**
     * @return HasOne<covariant (EntityContainerData|EntityPageData), $this>
     */
    abstract public function relatedData(): HasOne;

    /**
     * Get the attributes that are intended for the related contents model.
     * @return array<string, mixed>
     */
    protected function getContentsAttributes(): array
    {
        $contentFields = [];
        $contentModel = $this instanceof Page ? EntityPageData::class : EntityContainerData::class;

        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $contentModel::$fields)) {
                $contentFields[$key] = $value;
            }
        }

        return $contentFields;
    }

    /**
     * Create a new instance for the given entity type.
     */
    public static function instanceFromType(string $type): self
    {
        return match ($type) {
            'page' => new Page(),
            'chapter' => new Chapter(),
            'book' => new Book(),
            'bookshelf' => new Bookshelf(),
        };
    }
}
