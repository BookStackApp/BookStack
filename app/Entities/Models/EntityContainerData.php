<?php

namespace BookStack\Entities\Models;

use BookStack\Sorting\SortRule;
use BookStack\Uploads\Image;
use BookStack\Util\HtmlContentFilter;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $description
 * @property string $description_html
 * @property ?int    $default_template_id
 * @property ?int    $image_id
 * @property ?int    $sort_rule_id
 */
class EntityContainerData extends Model
{
    public $timestamps = false;

    /**
     * Relation for the cover image for this entity.
     * @return HasOne<Image, $this>
     */
    public function cover(): HasOne
    {
        return $this->hasOne(Image::class, 'image_id');
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
            return $this->cover?->getThumb($width, $height, false) ?? $default;
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
     * Get the description as a cleaned/handled HTML string.
     */
    public function getDescriptionHtml(bool $raw = false): string
    {
        $html = $this->description_html ?: '<p>' . nl2br(e($this->description)) . '</p>';
        if ($raw) {
            return $html;
        }

        return HtmlContentFilter::removeScriptsFromHtmlString($html);
    }

    /**
     * Update the description from HTML code.
     * Optionally takes plaintext to use for the model also.
     */
    public function setDescriptionHtml(string $html, string|null $plaintext = null): void
    {
        $this->description_html = $html;

        if ($plaintext !== null) {
            $this->description = $plaintext;
        }

        if (empty($html) && !empty($plaintext)) {
            $this->description_html = $this->getDescriptionHtml();
        }
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
