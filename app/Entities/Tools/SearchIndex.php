<?php namespace BookStack\Entities\Tools;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\SearchTerm;

class SearchIndex
{
    /**
     * @var SearchTerm
     */
    protected $searchTerm;

    /**
     * @var EntityProvider
     */
    protected $entityProvider;


    public function __construct(SearchTerm $searchTerm, EntityProvider $entityProvider)
    {
        $this->searchTerm = $searchTerm;
        $this->entityProvider = $entityProvider;
    }


    /**
     * Index the given entity.
     */
    public function indexEntity(Entity $entity)
    {
        $this->deleteEntityTerms($entity);
        $nameTerms = $this->generateTermArrayFromText($entity->name, 5 * $entity->searchFactor);
        $bodyTerms = $this->generateTermArrayFromText($entity->getText() ?? '', 1 * $entity->searchFactor);
        $terms = array_merge($nameTerms, $bodyTerms);
        foreach ($terms as $index => $term) {
            $terms[$index]['entity_type'] = $entity->getMorphClass();
            $terms[$index]['entity_id'] = $entity->id;
        }
        $this->searchTerm->newQuery()->insert($terms);
    }

    /**
     * Index multiple Entities at once
     * @param Entity[] $entities
     */
    protected function indexEntities(array $entities)
    {
        $terms = [];
        foreach ($entities as $entity) {
            $nameTerms = $this->generateTermArrayFromText($entity->name, 5 * $entity->searchFactor);
            $bodyTerms = $this->generateTermArrayFromText($entity->getText(), 1 * $entity->searchFactor);
            foreach (array_merge($nameTerms, $bodyTerms) as $term) {
                $term['entity_id'] = $entity->id;
                $term['entity_type'] = $entity->getMorphClass();
                $terms[] = $term;
            }
        }

        $chunkedTerms = array_chunk($terms, 500);
        foreach ($chunkedTerms as $termChunk) {
            $this->searchTerm->newQuery()->insert($termChunk);
        }
    }

    /**
     * Delete and re-index the terms for all entities in the system.
     */
    public function indexAllEntities()
    {
        $this->searchTerm->newQuery()->truncate();

        foreach ($this->entityProvider->all() as $entityModel) {
            $selectFields = ['id', 'name', $entityModel->textField];
            $entityModel->newQuery()
                ->withTrashed()
                ->select($selectFields)
                ->chunk(1000, function ($entities) {
                    $this->indexEntities($entities);
                });
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
                'term' => $token,
                'score' => $count * $scoreAdjustment
            ];
        }

        return $terms;
    }
}
