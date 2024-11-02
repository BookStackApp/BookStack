<?php

namespace BookStack\Exports\Controllers;

use BookStack\Exceptions\ZipValidationException;
use BookStack\Exports\ImportRepo;
use BookStack\Http\Controller;
use BookStack\Uploads\AttachmentService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct(
        protected ImportRepo $imports,
    ) {
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
            'file' => ['required', ...AttachmentService::getFileValidationRules()]
        ]);

        $file = $request->file('file');
        try {
            $import = $this->imports->storeFromUpload($file);
        } catch (ZipValidationException $exception) {
            session()->flash('validation_errors', $exception->errors);
            return redirect('/import');
        }

        return redirect("imports/{$import->id}");
    }
}
