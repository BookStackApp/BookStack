<?php

namespace BookStack\Sorting;

enum SortSetOption: string
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
}
