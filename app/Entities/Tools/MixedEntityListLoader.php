<?php

namespace BookStack\Entities\Tools;

use BookStack\App\Model;
use BookStack\Entities\Queries\EntityQueries;
use Illuminate\Database\Eloquent\Relations\Relation;

class MixedEntityListLoader
{
    public function __construct(
        protected EntityQueries $queries,
    ) {
    }

    /**
     * Efficiently load in entities for listing onto the given list
     * where entities are set as a relation via the given name.
     * This will look for a model id and type via 'name_id' and 'name_type'.
     * @param Model[] $relations
     */
    public function loadIntoRelations(array $relations, string $relationName, bool $loadParents): void
    {
        $idsByType = [];
        foreach ($relations as $relation) {
            $type = $relation->getAttribute($relationName . '_type');
            $id = $relation->getAttribute($relationName . '_id');

            if (!isset($idsByType[$type])) {
                $idsByType[$type] = [];
            }

            $idsByType[$type][] = $id;
        }

        $modelMap = $this->idsByTypeToModelMap($idsByType, $loadParents);

        foreach ($relations as $relation) {
            $type = $relation->getAttribute($relationName . '_type');
            $id = $relation->getAttribute($relationName . '_id');
            $related = $modelMap[$type][strval($id)] ?? null;
            if ($related) {
                $relation->setRelation($relationName, $related);
            }
        }
    }

    /**
     * @param array<string, int[]> $idsByType
     * @return array<string, array<int, Model>>
     */
    protected function idsByTypeToModelMap(array $idsByType, bool $eagerLoadParents): array
    {
        $modelMap = [];

        foreach ($idsByType as $type => $ids) {
            $models = $this->queries->visibleForList($type)
                ->whereIn('id', $ids)
                ->with($eagerLoadParents ? $this->getRelationsToEagerLoad($type) : [])
                ->get();

            if (count($models) > 0) {
                $modelMap[$type] = [];
            }

            foreach ($models as $model) {
                $modelMap[$type][strval($model->id)] = $model;
            }
        }

        return $modelMap;
    }

    protected function getRelationsToEagerLoad(string $type): array
    {
        $toLoad = [];
        $loadVisible = fn (Relation $query) => $query->scopes('visible');

        if ($type === 'chapter' || $type === 'page') {
            $toLoad['book'] = $loadVisible;
        }

        if ($type === 'page') {
            $toLoad['chapter'] = $loadVisible;
        }

        return $toLoad;
    }
}
