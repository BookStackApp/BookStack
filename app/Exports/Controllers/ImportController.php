<?php

namespace BookStack\Exports\Controllers;

use BookStack\Http\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:content-import');
    }

    public function start(Request $request)
    {
        return view('exports.import');
    }

    public function upload(Request $request)
    {
        // TODO
    }
}
