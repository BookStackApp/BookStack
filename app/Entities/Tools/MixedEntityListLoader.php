<?php

namespace BookStack\Entities\Tools;

use BookStack\App\Model;
use BookStack\Entities\EntityProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class MixedEntityListLoader
{
    protected array $listAttributes = [
        'page'      => ['id', 'name', 'slug', 'book_id', 'chapter_id', 'text', 'draft'],
        'chapter'   => ['id', 'name', 'slug', 'book_id', 'description'],
        'book'      => ['id', 'name', 'slug', 'description'],
        'bookshelf' => ['id', 'name', 'slug', 'description'],
    ];

    public function __construct(
        protected EntityProvider $entityProvider
    ) {
    }

    /**
     * Efficiently load in entities for listing onto the given list
     * where entities are set as a relation via the given name.
     * This will look for a model id and type via 'name_id' and 'name_type'.
     * @param Model[] $relations
     */
    public function loadIntoRelations(array $relations, string $relationName): void
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

        $modelMap = $this->idsByTypeToModelMap($idsByType);

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
    protected function idsByTypeToModelMap(array $idsByType): array
    {
        $modelMap = [];

        foreach ($idsByType as $type => $ids) {
            if (!isset($this->listAttributes[$type])) {
                continue;
            }

            $instance = $this->entityProvider->get($type);
            $models = $instance->newQuery()
                ->select($this->listAttributes[$type])
                ->scopes('visible')
                ->whereIn('id', $ids)
                ->with($this->getRelationsToEagerLoad($type))
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
