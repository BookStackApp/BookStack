<?php

namespace BookStack\Entities\Models;

use BookStack\Activity\Models\Loggable;
use BookStack\App\Model;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * Class EntityShareLink
 *
 * @property int $id
 * @property int $entity_id
 * @property string $entity_type
 * @property string $token
 * @property string|null $name
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property User $createdBy
 * @property Entity $entity
 */
class EntityShareLink extends Model implements Loggable
{
    use HasFactory;

    protected $fillable = ['name', 'token', 'entity_id', 'entity_type', 'created_by'];

    /**
     * Get the user that created this share link.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the entity that this share link belongs to.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    /**
     * Scope a query to only include links for a specific entity.
     */
    public function scopeForEntity(Builder $query, Entity $entity): Builder
    {
        return $query->where('entity_id', '=', $entity->id)
            ->where('entity_type', '=', $entity->getMorphClass());
    }

    /**
     * Generate a unique random token for share links.
     */
    public static function generateToken(): string
    {
        $token = Str::random(32);
        while (static::where('token', $token)->exists()) {
            $token = Str::random(32);
        }

        return $token;
    }

    /**
     * Get the public share URL for this link.
     */
    public function getUrl(): string
    {
        return url('/share/' . $this->token);
    }

    /**
     * {@inheritdoc}
     */
    public function logDescriptor(): string
    {
        $entityName = $this->entity ? $this->entity->name : "Entity#{$this->entity_id}";
        return "({$this->id}) {$this->name}; Entity: {$entityName}; User: {$this->createdBy->logDescriptor()}";
    }
}
