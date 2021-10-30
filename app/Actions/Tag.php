<?php

namespace BookStack\Actions;

use BookStack\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'order'];
    protected $hidden = ['id', 'entity_id', 'entity_type', 'created_at', 'updated_at'];

    /**
     * Get the entity that this tag belongs to.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    /**
     * Get a full URL to start a tag name search for this tag name.
     */
    public function nameUrl(): string
    {
        return url('/search?term=%5B' . urlencode($this->name) . '%5D');
    }

    /**
     * Get a full URL to start a tag name and value search for this tag's values.
     */
    public function valueUrl(): string
    {
        return url('/search?term=%5B' . urlencode($this->name) . '%3D' . urlencode($this->value) . '%5D');
    }
}
