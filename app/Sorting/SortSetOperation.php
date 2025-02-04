<?php

namespace BookStack\Sorting;

enum SortSetOperation: string
{
    case NameAsc = 'name_asc';
    case NameDesc = 'name_desc';
    case NameNumericAsc = 'name_numeric_asc';
    case CreatedDateAsc = 'created_date_asc';
    case CreatedDateDesc = 'created_date_desc';
    case UpdateDateAsc = 'updated_date_asc';
    case UpdateDateDesc = 'updated_date_desc';
    case ChaptersFirst = 'chapters_first';
    case ChaptersLast = 'chapters_last';

    /**
     * Provide a translated label string for this option.
     */
    public function getLabel(): string
    {
        $key = $this->value;
        $label = '';
        if (str_ends_with($key, '_asc')) {
            $key = substr($key, 0, -4);
            $label = trans('settings.sort_set_op_asc');
        } elseif (str_ends_with($key, '_desc')) {
            $key = substr($key, 0, -5);
            $label = trans('settings.sort_set_op_desc');
        }

        $label = trans('settings.sort_set_op_' . $key) . ' ' . $label;
        return trim($label);
    }

    /**
     * @return SortSetOperation[]
     */
    public static function allExcluding(array $operations): array
    {
        $all = SortSetOperation::cases();
        $filtered = array_filter($all, function (SortSetOperation $operation) use ($operations) {
            return !in_array($operation, $operations);
        });
        return array_values($filtered);
    }

    /**
     * Create a set of operations from a string sequence representation.
     * (values seperated by commas).
     * @return SortSetOperation[]
     */
    public static function fromSequence(string $sequence): array
    {
        $strOptions = explode(',', $sequence);
        $options = array_map(fn ($val) => SortSetOperation::tryFrom($val), $strOptions);
        return array_filter($options);
    }
}
