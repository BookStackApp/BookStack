<?php

namespace BookStack\Exports\Controllers;

use BookStack\Exports\Import;
use BookStack\Exports\ZipExports\ZipExportReader;
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

        return view('exports.import', [
            'zipErrors' => session()->pull('validation_errors') ?? [],
        ]);
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
            session()->flash('validation_errors', $errors);
            return redirect('/import');
        }

        $zipEntityInfo = (new ZipExportReader($zipPath))->getEntityInfo();
        $import = new Import();
        $import->name = $zipEntityInfo['name'];
        $import->book_count = $zipEntityInfo['book_count'];
        $import->chapter_count = $zipEntityInfo['chapter_count'];
        $import->page_count = $zipEntityInfo['page_count'];
        $import->created_by = user()->id;
        $import->size = filesize($zipPath);
        // TODO - Set path
        // TODO - Save

        // TODO - Split out attachment service to separate out core filesystem/disk stuff
        //        To reuse for import handling

        dd('passed');
        // TODO - Upload to storage
        // TODO - Store info/results for display:
        // TODO - Send user to next import stage
    }
}
