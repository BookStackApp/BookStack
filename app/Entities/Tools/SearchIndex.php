<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\SearchTerm;
use Illuminate\Support\Collection;

class SearchIndex
{

    /**
     * @var EntityProvider
     */
    protected $entityProvider;

    public function __construct(EntityProvider $entityProvider)
    {
        $this->entityProvider = $entityProvider;
    }

    /**
     * Index the given entity.
     */
    public function indexEntity(Entity $entity)
    {
        $this->deleteEntityTerms($entity);
        $terms = $this->entityToTermDataArray($entity);
        SearchTerm::query()->insert($terms);
    }

    /**
     * Index multiple Entities at once.
     *
     * @param Entity[] $entities
     */
    public function indexEntities(array $entities)
    {
        $terms = [];
        foreach ($entities as $entity) {
            $entityTerms = $this->entityToTermDataArray($entity);
            array_push($terms, ...$entityTerms);
        }

        $chunkedTerms = array_chunk($terms, 500);
        foreach ($chunkedTerms as $termChunk) {
            SearchTerm::query()->insert($termChunk);
        }
    }

    /**
     * Delete and re-index the terms for all entities in the system.
     * Can take a callback which is used for reporting progress.
     * Callback receives three arguments:
     * - An instance of the model being processed
     * - The number that have been processed so far.
     * - The total number of that model to be processed.
     *
     * @param callable(Entity, int, int)|null $progressCallback
     */
    public function indexAllEntities(?callable $progressCallback = null)
    {
        SearchTerm::query()->truncate();

        foreach ($this->entityProvider->all() as $entityModel) {
            $selectFields = ['id', 'name', $entityModel->textField];
            $total = $entityModel->newQuery()->withTrashed()->count();
            $chunkSize = 250;
            $processed = 0;

            $chunkCallback = function (Collection $entities) use ($progressCallback, &$processed, $total, $chunkSize, $entityModel) {
                $this->indexEntities($entities->all());
                $processed = min($processed + $chunkSize, $total);

                if (is_callable($progressCallback)) {
                    $progressCallback($entityModel, $processed, $total);
                }
            };

            $entityModel->newQuery()
                ->select($selectFields)
                ->chunk($chunkSize, $chunkCallback);
        }
    }

    /**
     * Delete related Entity search terms.
     */
    public function deleteEntityTerms(Entity $entity)
    {
        $entity->searchTerms()->delete();
    }

    /**
     * Create a scored term array from the given text.
     *
     * @returns array{term: string, score: float}
     */
    protected function generateTermArrayFromText(string $text, int $scoreAdjustment = 1): array
    {
        $tokenMap = []; // {TextToken => OccurrenceCount}
        $splitChars = " \n\t.,!?:;()[]{}<>`'\"";
        $token = strtok($text, $splitChars);

        while ($token !== false) {
            if (!isset($tokenMap[$token])) {
                $tokenMap[$token] = 0;
            }
            $tokenMap[$token]++;
            $token = strtok($splitChars);
        }

        $terms = [];
        foreach ($tokenMap as $token => $count) {
            $terms[] = [
                'term'  => $token,
                'score' => $count * $scoreAdjustment,
            ];
        }

        return $terms;
    }

    /**
     * For the given entity, Generate an array of term data details.
     * Is the raw term data, not instances of SearchTerm models.
     *
     * @returns array{term: string, score: float}[]
     */
    protected function entityToTermDataArray(Entity $entity): array
    {
        $nameTerms = $this->generateTermArrayFromText($entity->name, 40 * $entity->searchFactor);
        $bodyTerms = $this->generateTermArrayFromText($entity->getText(), 1 * $entity->searchFactor);
        $termData = array_merge($nameTerms, $bodyTerms);

        foreach ($termData as $index => $term) {
            $termData[$index]['entity_type'] = $entity->getMorphClass();
            $termData[$index]['entity_id'] = $entity->id;
        }

        return $termData;
    }
}
