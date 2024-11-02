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

        dd('passed');
        // TODO - Upload to storage
        // TODO - Store info/results for display:
          // - zip_path
          // - name (From name of thing being imported)
          // - size
          // - book_count
          // - chapter_count
          // - page_count
          // - created_by
          // - created_at/updated_at
        // TODO - Send user to next import stage
    }
}
