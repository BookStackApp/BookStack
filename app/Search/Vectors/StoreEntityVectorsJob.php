<?php

namespace BookStack\Search\Vectors;

use BookStack\Entities\Models\Entity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreEntityVectorsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Entity $entity
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(EntityVectorGenerator $generator): void
    {
        $generator->generateAndStore($this->entity);
    }
}
