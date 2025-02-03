<?php

namespace BookStack\Sorting;

use BookStack\Http\Controller;

class SortSetController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:settings-manage');
        // TODO - Test
    }

    public function create()
    {
        return view('settings.sort-sets.create');
    }
}
