<?php

namespace BookStack\Sorting;

use voku\helper\ASCII;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;

/**
 * Sort comparison function for each of the possible SortSetOperation values.
 * Method names should be camelCase names for the SortSetOperation enum value.
 */
class SortSetOperationComparisons
{
    public static function nameAsc(Entity $a, Entity $b): int
    {
        return strtolower(ASCII::to_transliterate($a->name, null)) <=> strtolower(ASCII::to_transliterate($b->name, null));
    }

    public static function nameDesc(Entity $a, Entity $b): int
    {
        return strtolower(ASCII::to_transliterate($b->name, null)) <=> strtolower(ASCII::to_transliterate($a->name, null));
    }

    public static function nameNumericAsc(Entity $a, Entity $b): int
    {
        $numRegex = '/^\d+(\.\d+)?/';
        $aMatches = [];
        $bMatches = [];
        preg_match($numRegex, $a->name, $aMatches);
        preg_match($numRegex, $b->name, $bMatches);
        $aVal = floatval(($aMatches[0] ?? 0));
        $bVal = floatval(($bMatches[0] ?? 0));

        return $aVal <=> $bVal;
    }

    public static function nameNumericDesc(Entity $a, Entity $b): int
    {
        return -(static::nameNumericAsc($a, $b));
    }

    public static function createdDateAsc(Entity $a, Entity $b): int
    {
        return $a->created_at->unix() <=> $b->created_at->unix();
    }

    public static function createdDateDesc(Entity $a, Entity $b): int
    {
        return $b->created_at->unix() <=> $a->created_at->unix();
    }

    public static function updatedDateAsc(Entity $a, Entity $b): int
    {
        return $a->updated_at->unix() <=> $b->updated_at->unix();
    }

    public static function updatedDateDesc(Entity $a, Entity $b): int
    {
        return $b->updated_at->unix() <=> $a->updated_at->unix();
    }

    public static function chaptersFirst(Entity $a, Entity $b): int
    {
        return ($b instanceof Chapter ? 1 : 0) - (($a instanceof Chapter) ? 1 : 0);
    }

    public static function chaptersLast(Entity $a, Entity $b): int
    {
        return ($a instanceof Chapter ? 1 : 0) - (($b instanceof Chapter) ? 1 : 0);
    }
}
