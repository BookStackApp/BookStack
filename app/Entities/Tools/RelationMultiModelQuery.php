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
    /** @var array<string, array> */
    protected $lookupModels = [];

    /** @var Model */
    protected $relation;

    /** @var string */
    protected $polymorphicFieldName;

    /**
     * The keys are relation fields to fetch.
     * The values are the name to use for the resulting model attribute.
     * @var array<string, string>
     */
    protected $relationFields = [];

    /**
     * An array of [string $col, string $operator, mixed $value] where conditions.
     * @var array<array>>
     */
    protected $relationWheres = [];

    /**
     * Field on the relation field to order by.
     * @var ?array[string $column, string $direction]
     */
    protected $orderByRelationField = null;

    /**
     * Number of results to take
     * @var ?int
     */
    protected $take = null;

    /**
     * Number of results to skip.
     * @var ?int
     */
    protected $skip = null;

    /**
     * Callback that will receive the query for any advanced customization.
     * @var ?callable
     */
    protected $queryCustomizer = null;

    /**
     * @throws \Exception
     */
    public function __construct(string $relation, string $polymorphicFieldName)
    {
        $this->relation = (new $relation);
        if (!$this->relation instanceof Model) {
            throw new \Exception('Given relation must be a model instance class');
        }
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
     * Bring back a field from the relation object with the model results.
     */
    public function withRelationField(string $fieldName, string $modelAttributeName): self
    {
        $this->relationFields[$fieldName] = $modelAttributeName;
        return $this;
    }

    /**
     * Add a where condition to the query for the main relation table.
     */
    public function whereRelation(string $column, string $operator, $value): self
    {
        $this->relationWheres[] = [$column, $operator, $value];
        return $this;
    }

    /**
     * Order by the given relation column.
     */
    public function orderByRelation(string $column, string $direction = 'asc'): self
    {
        $this->orderByRelationField = [$column, $direction];
        return $this;
    }

    /**
     * Skip the given $count of results in the query.
     */
    public function skip(?int $count): self
    {
        $this->skip = $count;
        return $this;
    }

    /**
     * Take the given $count of results in the query.
     */
    public function take(?int $count): self
    {
        $this->take = $count;
        return $this;
    }

    /**
     * Pass a callable, which will receive the base query
     * to perform additional custom operations on the query.
     */
    public function customizeUsing(callable $customizer): self
    {
        $this->queryCustomizer = $customizer;
        return $this;
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
        $results = $this->build()->get();
        return $this->hydrateModelsFromResults($results);
    }

    /**
     * Build the core query to run.
     */
    protected function build(): Builder
    {
        $query = $this->relation->newQuery()->toBase();
        $relationTable = $this->relation->getTable();
        $modelTables = [];

        // Load relation fields
        foreach ($this->relationFields as $relationField => $alias) {
            $query->addSelect(
                $relationTable . '.' . $relationField . ' as '
                . $relationTable . '@' . $relationField
            );
        }

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

        // Add relation wheres
        foreach ($this->relationWheres as [$column, $operator, $value]) {
            $query->where($relationTable . '.' . $column, $operator, $value);
        }

        // Skip and take
        if (!is_null($this->skip)) {
            $query->skip($this->skip);
        }
        if (!is_null($this->take)) {
            $query->take($this->take);
        }
        if (!is_null($this->queryCustomizer)) {
            $customizer = $this->queryCustomizer;
            $customizer($query);
        }
        if (!is_null($this->orderByRelationField)) {
            $query->orderBy($relationTable . '.' . $this->orderByRelationField[0], $this->orderByRelationField[1]);
        }

        $this->applyPermissionsToQuery($query, 'view');

        return $query;
    }

    /**
     * Run the query through the permission system.
     */
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
            $selectArray[] = $table . '.' . $column . ' as ' . $table . '@' . $column;
        }
        return $selectArray;
    }

    /**
     * Hydrate a collection of result data into models.
     */
    protected function hydrateModelsFromResults(Collection $results): Collection
    {
        $modelByIdColumn = [];
        foreach ($this->lookupModels as $lookupModel => $columns) {
            /** @var Model $model */
            $model = new $lookupModel;
            $modelByIdColumn[$model->getTable() . '@id'] = $model;
        }

        return $results->map(function ($result) use ($modelByIdColumn) {
            foreach ($modelByIdColumn as $idColumn => $modelInstance) {
                if (isset($result->$idColumn)) {
                    return $this->hydrateModelFromResult($modelInstance, $result);
                }
            }
            return null;
        });
    }

    /**
     * Hydrate the given model type with the database result.
     */
    protected function hydrateModelFromResult(Model $model, \stdClass $result): Model
    {
        $modelPrefix = $model->getTable() . '@';
        $relationPrefix = $this->relation->getTable() . '@';
        $attrs = [];

        foreach ((array) $result as $col => $value) {
            if (strpos($col, $modelPrefix) === 0) {
                $attrName = substr($col, strlen($modelPrefix));
                $attrs[$attrName] = $value;
            }
            if (strpos($col, $relationPrefix) === 0) {
                $col = substr($col, strlen($relationPrefix));
                $attrName = $this->relationFields[$col];
                $attrs[$attrName] = $value;
            }
        }

        return $model->newInstance()->forceFill($attrs);
    }
}
