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
     * @return SortSetOption[]
     */
    public function getOptions(): array
    {
        $strOptions = explode(',', $this->sequence);
        $options = array_map(fn ($val) => SortSetOption::tryFrom($val), $strOptions);
        return array_filter($options);
    }

    /**
     * @param SortSetOption[] $options
     */
    public function setOptions(array $options): void
    {
        $values = array_map(fn (SortSetOption $opt) => $opt->value, $options);
        $this->sequence = implode(',', $values);
    }
}
