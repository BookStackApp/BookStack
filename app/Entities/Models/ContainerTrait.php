<?php

namespace BookStack\Entities\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin Entity
 */
trait ContainerTrait
{
    /**
     * @return HasOne<EntityContainerContents, $this>
     */
    public function relatedData(): HasOne
    {
        return $this->hasOne(EntityContainerContents::class, 'entity_id', 'id')
            ->where('entity_type', '=', $this->getMorphClass());
    }

    public function contents(): EntityContainerContents
    {
        $data = parent::contents();
        if ($data instanceof EntityContainerContents) {
            return $data;
        }

        /** @var EntityContainerContents $data */
        $data = $this->relatedData()->newModelInstance();
        $data->setRawAttributes([
            'entity_id' => $this->id,
            'entity_type' => $this->getMorphClass(),
            'description' => '',
            'description_html' => '',
            'default_template_id' => null,
            'image_id' => null,
            'sort_rule_id' => null,
        ]);

        return $data;
    }
}
