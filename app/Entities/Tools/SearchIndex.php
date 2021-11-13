<?php

namespace BookStack\Entities\Tools;

use BookStack\Actions\Tag;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\SearchTerm;
use DOMDocument;
use DOMNode;
use Illuminate\Support\Collection;

class SearchIndex
{
    /**
     * A list of delimiter characters used to break-up parsed content into terms for indexing.
     *
     * @var string
     */
    public static $delimiters = " \n\t.,!?:;()[]{}<>`'\"";

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
            $indexContentField = $entityModel instanceof Page ? 'html' : 'description';
            $selectFields = ['id', 'name', $indexContentField];
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
                ->with(['tags:id,name,value,entity_id,entity_type'])
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
     * Create a scored term array from the given text, where the keys are the terms
     * and the values are their scores.
     *
     * @returns array<string, int>
     */
    protected function generateTermScoreMapFromText(string $text, int $scoreAdjustment = 1): array
    {
        $termMap = $this->textToTermCountMap($text);

        foreach ($termMap as $term => $count) {
            $termMap[$term] = $count * $scoreAdjustment;
        }

        return $termMap;
    }

    /**
     * Create a scored term array from the given HTML, where the keys are the terms
     * and the values are their scores.
     *
     * @returns array<string, int>
     */
    protected function generateTermScoreMapFromHtml(string $html): array
    {
        if (empty($html)) {
            return [];
        }

        $scoresByTerm = [];
        $elementScoreAdjustmentMap = [
            'h1' => 10,
            'h2' => 5,
            'h3' => 4,
            'h4' => 3,
            'h5' => 2,
            'h6' => 1.5,
        ];

        $html = '<body>' . $html . '</body>';
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $topElems = $doc->documentElement->childNodes->item(0)->childNodes;
        /** @var DOMNode $child */
        foreach ($topElems as $child) {
            $nodeName = $child->nodeName;
            $termCounts = $this->textToTermCountMap(trim($child->textContent));
            foreach ($termCounts as $term => $count) {
                $scoreChange = $count * ($elementScoreAdjustmentMap[$nodeName] ?? 1);
                $scoresByTerm[$term] = ($scoresByTerm[$term] ?? 0) + $scoreChange;
            }
        }

        return $scoresByTerm;
    }

    /**
     * Create a scored term map from the given set of entity tags.
     *
     * @param Tag[] $tags
     *
     * @returns array<string, int>
     */
    protected function generateTermScoreMapFromTags(array $tags): array
    {
        $scoreMap = [];
        $names = [];
        $values = [];

        foreach ($tags as $tag) {
            $names[] = $tag->name;
            $values[] = $tag->value;
        }

        $nameMap = $this->generateTermScoreMapFromText(implode(' ', $names), 3);
        $valueMap = $this->generateTermScoreMapFromText(implode(' ', $values), 5);

        return $this->mergeTermScoreMaps($nameMap, $valueMap);
    }

    /**
     * For the given text, return an array where the keys are the unique term words
     * and the values are the frequency of that term.
     *
     * @returns array<string, int>
     */
    protected function textToTermCountMap(string $text): array
    {
        $tokenMap = []; // {TextToken => OccurrenceCount}
        $splitChars = static::$delimiters;
        $token = strtok($text, $splitChars);

        while ($token !== false) {
            if (!isset($tokenMap[$token])) {
                $tokenMap[$token] = 0;
            }
            $tokenMap[$token]++;
            $token = strtok($splitChars);
        }

        return $tokenMap;
    }

    /**
     * For the given entity, Generate an array of term data details.
     * Is the raw term data, not instances of SearchTerm models.
     *
     * @returns array{term: string, score: float, entity_id: int, entity_type: string}[]
     */
    protected function entityToTermDataArray(Entity $entity): array
    {
        $nameTermsMap = $this->generateTermScoreMapFromText($entity->name, 40 * $entity->searchFactor);
        $tagTermsMap = $this->generateTermScoreMapFromTags($entity->tags->all());

        if ($entity instanceof Page) {
            $bodyTermsMap = $this->generateTermScoreMapFromHtml($entity->html);
        } else {
            $bodyTermsMap = $this->generateTermScoreMapFromText($entity->description, $entity->searchFactor);
        }

        $mergedScoreMap = $this->mergeTermScoreMaps($nameTermsMap, $bodyTermsMap, $tagTermsMap);

        $dataArray = [];
        $entityId = $entity->id;
        $entityType = $entity->getMorphClass();
        foreach ($mergedScoreMap as $term => $score) {
            $dataArray[] = [
                'term'        => $term,
                'score'       => $score,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
            ];
        }

        return $dataArray;
    }

    /**
     * For the given term data arrays, Merge their contents by term
     * while combining any scores.
     *
     * @param array<string, int>[] ...$scoreMaps
     *
     * @returns array<string, int>
     */
    protected function mergeTermScoreMaps(...$scoreMaps): array
    {
        $mergedMap = [];

        foreach ($scoreMaps as $scoreMap) {
            foreach ($scoreMap as $term => $score) {
                $mergedMap[$term] = ($mergedMap[$term] ?? 0) + $score;
            }
        }

        return $mergedMap;
    }
}
