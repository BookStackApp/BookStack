<?php

namespace BookStack\Search\Vectors;

use BookStack\Entities\Models\Entity;
use BookStack\Search\Vectors\Services\VectorQueryService;
use Illuminate\Support\Facades\DB;

class EntityVectorGenerator
{
    public function __construct(
        protected VectorQueryServiceProvider $vectorQueryServiceProvider
    ) {
    }

    public function generateAndStore(Entity $entity): void
    {
        $vectorService = $this->vectorQueryServiceProvider->get();

        $text = $this->entityToPlainText($entity);
        $chunks = $this->chunkText($text);
        $embeddings = $this->chunksToEmbeddings($chunks, $vectorService);

        $this->deleteExistingEmbeddingsForEntity($entity);
        $this->storeEmbeddings($embeddings, $chunks, $entity);
    }

    protected function deleteExistingEmbeddingsForEntity(Entity $entity): void
    {
        SearchVector::query()
            ->where('entity_type', '=', $entity->getMorphClass())
            ->where('entity_id', '=', $entity->id)
            ->delete();
    }

    protected function storeEmbeddings(array $embeddings, array $textChunks, Entity $entity): void
    {
        $toInsert = [];

        foreach ($embeddings as $index => $embedding) {
            $text = $textChunks[$index];
            $toInsert[] = [
                'entity_id' => $entity->id,
                'entity_type' => $entity->getMorphClass(),
                'embedding' => DB::raw('VEC_FROMTEXT("[' . implode(',', $embedding) . ']")'),
                'text' => $text,
            ];
        }

        // TODO - Chunk inserts
        SearchVector::query()->insert($toInsert);
    }

    /**
     * @param string[] $chunks
     * @return float[] array
     */
    protected function chunksToEmbeddings(array $chunks, VectorQueryService $vectorQueryService): array
    {
        $embeddings = [];
        foreach ($chunks as $index => $chunk) {
            $embeddings[$index] = $vectorQueryService->generateEmbeddings($chunk);
        }
        return $embeddings;
    }

    /**
     * @return string[]
     */
    protected function chunkText(string $text): array
    {
        // TODO - Join adjacent smaller chunks up
        return array_filter(array_map(function (string $section): string {
            return trim($section);
        }, explode("\n", $text)));
    }

    protected function entityToPlainText(Entity $entity): string
    {
        $text = $entity->name . "\n\n" . $entity->{$entity->textField};
        // TODO - Add tags
        return $text;
    }
}
