<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use BookStack\Search\Vectors\SearchVector;
use BookStack\Search\Vectors\StoreEntityVectorsJob;
use Illuminate\Console\Command;

class RegenerateVectorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:regenerate-vectors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-index vectors for all content in the system';

    /**
     * Execute the console command.
     */
    public function handle(EntityProvider $entityProvider)
    {
        // TODO - Add confirmation before run regarding deletion/time/effort/api-cost etc...
        SearchVector::query()->delete();

        $types = $entityProvider->all();
        foreach ($types as $type => $typeInstance) {
            $this->info("Creating jobs to store vectors for {$type} data...");
            /** @var Entity[] $entities  */
            $typeInstance->newQuery()->chunkById(100, function ($entities) {
                foreach ($entities as $entity) {
                    dispatch(new StoreEntityVectorsJob($entity));
                }
            });
        }
    }
}
