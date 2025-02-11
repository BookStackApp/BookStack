<?php

namespace BookStack\Sorting;

use BookStack\Activity\Models\Loggable;
use BookStack\Entities\Models\Book;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $sequence
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SortRule extends Model implements Loggable
{
    use HasFactory;

    /**
     * @return SortRuleOperation[]
     */
    public function getOperations(): array
    {
        return SortRuleOperation::fromSequence($this->sequence);
    }

    /**
     * @param SortRuleOperation[] $options
     */
    public function setOperations(array $options): void
    {
        $values = array_map(fn (SortRuleOperation $opt) => $opt->value, $options);
        $this->sequence = implode(',', $values);
    }

    public function logDescriptor(): string
    {
        return "({$this->id}) {$this->name}";
    }

    public function getUrl(): string
    {
        return url("/settings/sorting/rules/{$this->id}");
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public static function allByName(): Collection
    {
        return static::query()
            ->withCount('books')
            ->orderBy('name', 'asc')
            ->get();
    }
}
