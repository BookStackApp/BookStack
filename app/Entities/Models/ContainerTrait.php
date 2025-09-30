<?php

namespace BookStack\Entities\Models;

use BookStack\Entities\Tools\EntityHtmlDescription;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin Entity
 */
trait ContainerTrait
{
    public function description(): EntityHtmlDescription
    {
        return new EntityHtmlDescription($this);
    }

    /**
     * @return HasOne<EntityContainerContents, $this>
     */
    public function relatedData(): HasOne
    {
        return $this->hasOne(EntityContainerContents::class, 'entity_id', 'id')
            ->where('entity_type', '=', $this->getMorphClass());
    }
}
