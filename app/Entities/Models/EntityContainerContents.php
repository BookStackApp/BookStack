<?php

namespace BookStack\Entities\Models;

use BookStack\Sorting\SortRule;
use BookStack\Uploads\Image;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $entity_id
 * @property string $entity_type
 * @property string $description
 * @property string $description_html
 * @property ?int    $default_template_id
 * @property ?int    $image_id
 * @property ?int    $sort_rule_id
 */
class EntityContainerContents extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'entity_id';
    public $incrementing = false;

    // TODO - Should put the entity methods and relations back onto the original models
    //    if we're going back to mostly keeping to the models.

    /**
     * Override the default set keys for save query method to make it work with composite keys.
     */
    public function setKeysForSaveQuery($query): Builder
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery())
            ->where('type', '=', $this->entity_type);

        return $query;
    }

    /**
     * Override the default set keys for select query method to make it work with composite keys.
     */
    protected function setKeysForSelectQuery($query): Builder
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSelectQuery())
            ->where('type', '=', $this->entity_type);

        return $query;
    }

    /**
     * Relation for the cover image for this entity.
     * @return HasOne<Image, $this>
     */
    public function cover(): HasOne
    {
        return $this->hasOne(Image::class, 'image_id');
    }

    public function getCover(): Image|null
    {
        return $this->cover()->first();
    }

    /**
     * Returns a shelf cover image URL, if cover not exists return default cover image.
     */
    public function getCoverUrl(int $width = 440, int $height = 250, string|null $default = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='): string|null
    {
        if (!$this->image_id) {
            return $default;
        }

        try {
            return $this->getCover()?->getThumb($width, $height, false) ?? $default;
        } catch (Exception $err) {
            return $default;
        }
    }

    /**
     * Check if this data supports having a default template assigned.
     */
    public function supportsDefaultTemplate(): bool
    {
        return in_array($this->entity_type, ['book', 'chapter']);
    }

    /**
     * Check this data supports having a cover image assigned.
     */
    public function supportsCoverImage(): bool
    {
        return in_array($this->entity_type, ['book', 'bookshelf']);
    }

    /**
     * Get the Page used as a default template to be used for new items within this container.
     */
    public function defaultTemplate(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'default_template_id');
    }

    /**
     * Get the sort rule assigned to this container, if existing.
     */
    public function sortRule(): BelongsTo
    {
        return $this->belongsTo(SortRule::class);
    }
}
