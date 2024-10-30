<?php

namespace BookStack\Exports\Controllers;

use BookStack\Exports\ZipExports\ZipExportValidator;
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
        // TODO - Show existing imports for user (or for all users if admin-level user)

        return view('exports.import');
    }

    public function upload(Request $request)
    {
        $this->validate($request, [
            'file' => ['required', 'file']
        ]);

        $file = $request->file('file');
        $zipPath = $file->getRealPath();

        $errors = (new ZipExportValidator($zipPath))->validate();
        if ($errors) {
            dd($errors);
        }
        dd('passed');
        // TODO - Read existing ZIP upload and send through validator
            // TODO - If invalid, return user with errors
        // TODO - Upload to storage
        // TODO - Store info/results from validator
        // TODO - Send user to next import stage
    }
}
