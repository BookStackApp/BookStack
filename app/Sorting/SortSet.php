<?php

namespace BookStack\Sorting;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $sequence
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SortSet extends Model
{
    /**
     * @return SortSetOperation[]
     */
    public function getOperations(): array
    {
        $strOptions = explode(',', $this->sequence);
        $options = array_map(fn ($val) => SortSetOperation::tryFrom($val), $strOptions);
        return array_filter($options);
    }

    /**
     * @param SortSetOperation[] $options
     */
    public function setOperations(array $options): void
    {
        $values = array_map(fn (SortSetOperation $opt) => $opt->value, $options);
        $this->sequence = implode(',', $values);
    }
}
