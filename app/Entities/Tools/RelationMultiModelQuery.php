<?php namespace BookStack\Entities\Tools;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

/**
 * Create a query for a polymorphic relation
 * that looks up all entity models in a single query
 * returning a collection or various hydrated models.
 */
class RelationMultiModelQuery
{

    // TODO - Hydrate results to models
    // TODO - Allow setting additional wheres and all-model columns (From the core relation - eg, last_viewed_at)

//select views.updated_at as last_viewed_at,
//b.id as book_id, b.name as book_name, b.slug as book_slug, b.description as book_description,
//s.id as bookshelf_id, s.name as bookshelf_name, s.slug as bookshelf_slug, s.description as bookshelf_description,
//c.id as chapter_id, c.name as chapter_name, c.slug as chapter_slug, c.description as chapter_description,
//p.id as page_id, p.name as page_name, p.slug as page_slug, p.text as page_description
//from views
//left join bookshelves s on (s.id = views.viewable_id and views.viewable_type = 'BookStack\\Bookshelf' and s.deleted_at is null)
//left join books b on (b.id = views.viewable_id and views.viewable_type = 'BookStack\\Book' and b.deleted_at is null)
//left join chapters c on (c.id = views.viewable_id and views.viewable_type = 'BookStack\\Chapter' and c.deleted_at is null)
//left join pages p on (p.id = views.viewable_id and views.viewable_type = 'BookStack\\Page' and p.deleted_at is null)
//#     Permissions
//where exists(
//select * from joint_permissions jp where jp.entity_id = views.viewable_id and jp.entity_type = views.viewable_type
//and jp.action = 'view' and jp.role_id in (1, 2, 3, 6, 12) and (jp.has_permission = 1 or (jp.has_permission_own = 1 and jp.owned_by = 1))
//)
//and (s.id is not null or b.id is not null or c.id is not null or p.id is not null)
//and views.user_id = 1

    /** @var array<string, array> */
    protected $lookupModels = [];

    /** @var Model */
    protected $relation;

    /** @var string */
    protected $polymorphicFieldName;

    public function __construct(Model $relation, string $polymorphicFieldName)
    {
        $this->relation = $relation;
        $this->polymorphicFieldName = $polymorphicFieldName;
    }

    /**
     * Set the query to look up the given entity type.
     */
    public function forEntity(string $class, array $columns): self
    {
        $this->lookupModels[$class] = $columns;
        return $this;
    }

    /**
     * Set the query to look up all entity types.
     */
    public function forAllEntities(): self
    {
        $this->lookupModels[Page::class] = ['id', 'name', 'slug', 'book_id', 'chapter_id', 'text'];
        $this->lookupModels[Chapter::class] = ['id', 'name', 'slug', 'book_id', 'description'];
        $this->lookupModels[Book::class] = ['id', 'name', 'slug', 'description', 'image_id'];
        $this->lookupModels[Bookshelf::class] = ['id', 'name', 'slug', 'description', 'image_id'];
        return $this;
    }

    /**
     * Build the core query to run.
     */
    protected function build(): Builder
    {
        $query = $this->relation->newQuery()->toBase();
        $relationTable = $this->relation->getTable();
        $modelTables = [];

        // Load model selects & joins
        foreach ($this->lookupModels as $lookupModel => $columns) {
            /** @var Entity $model */
            $model = (new $lookupModel);
            $table = $model->getTable();
            $modelTables[] = $table;
            $query->addSelect($this->tableColumnsToSelectArray($table, $columns));
            $query->leftJoin($table, function (JoinClause $join) use ($table, $relationTable, $model) {
                $polyPrefix = $relationTable . '.' . $this->polymorphicFieldName;
                $join->on($polyPrefix . '_id', '=', $table . '.id');
                $join->where($polyPrefix . '_type', '=', $model->getMorphClass());
                $join->whereNull($table . '.deleted_at');
            });
        }

        // Where we have a model result
        $query->where(function (Builder $query) use ($modelTables) {
            foreach ($modelTables as $table) {
                $query->orWhereNotNull($table . '.id');
            }
        });

        $this->applyPermissionsToQuery($query, 'view');

        return $query;
    }

    protected function applyPermissionsToQuery(Builder $query, string $action)
    {
        $permissions = app()->make(PermissionService::class);
        $permissions->filterRestrictedEntityRelations(
            $query,
            $this->relation->getTable(),
            $this->polymorphicFieldName . '_id',
            $this->polymorphicFieldName . '_type',
            $action,
        );
    }

    /**
     * Create an array of select statements from the given table and column.
     */
    protected function tableColumnsToSelectArray(string $table, array $columns): array
    {
        $selectArray = [];
        foreach ($columns as $column) {
            $selectArray[] = $table . '.' . $column . ' as '.  $table . '_' . $column;
        }
        return $selectArray;
    }

    /**
     * Get the SQL from the core query being ran.
     */
    public function toSql(): string
    {
        return $this->build()->toSql();
    }

    /**
     * Run the query and get the results.
     */
    public function run(): Collection
    {
        return $this->build()->get();
    }
}