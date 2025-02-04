<?php

namespace BookStack\Sorting;

use BookStack\Activity\Models\Loggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $sequence
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SortSet extends Model implements Loggable
{
    /**
     * @return SortSetOperation[]
     */
    public function getOperations(): array
    {
        return SortSetOperation::fromSequence($this->sequence);
    }

    /**
     * @param SortSetOperation[] $options
     */
    public function setOperations(array $options): void
    {
        $values = array_map(fn (SortSetOperation $opt) => $opt->value, $options);
        $this->sequence = implode(',', $values);
    }

    public function logDescriptor(): string
    {
        return "({$this->id}) {$this->name}";
    }

    public function getUrl(): string
    {
        return url("/settings/sorting/sets/{$this->id}");
    }
}
