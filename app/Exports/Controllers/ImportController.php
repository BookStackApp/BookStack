<?php

declare(strict_types=1);

namespace BookStack\Exports\Controllers;

use BookStack\Activity\ActivityType;
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

    /**
     * Show the view to start a new import, and also list out the existing
     * in progress imports that are visible to the user.
     */
    public function start()
    {
        $imports = $this->imports->getVisibleImports();

        $this->setPageTitle(trans('entities.import'));

        return view('exports.import', [
            'imports' => $imports,
            'zipErrors' => session()->pull('validation_errors') ?? [],
        ]);
    }

    /**
     * Upload, validate and store an import file.
     */
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

        $this->logActivity(ActivityType::IMPORT_CREATE, $import);

        return redirect($import->getUrl());
    }

    /**
     * Show a pending import, with a form to allow progressing
     * with the import process.
     */
    public function show(int $id)
    {
        $import = $this->imports->findVisible($id);

        $this->setPageTitle(trans('entities.import_continue'));

        return view('exports.import-show', [
            'import' => $import,
        ]);
    }

    public function run(int $id, Request $request)
    {
        // TODO - Test access/visibility
        $import = $this->imports->findVisible($id);
        $parent = null;

        if ($import->getType() === 'page' || $import->getType() === 'chapter') {
            $data = $this->validate($request, [
                'parent' => ['required', 'string']
            ]);
            $parent = $data['parent'];
        }

        // TODO  - Run import
           // TODO - Validate again before
           // TODO - Check permissions before (create for main item, create for children, create for related items [image, attachments])
        // TODO - Redirect to result
        // TOOD - Or redirect back with errors
    }

    /**
     * Delete an active pending import from the filesystem and database.
     */
    public function delete(int $id)
    {
        $import = $this->imports->findVisible($id);
        $this->imports->deleteImport($import);

        $this->logActivity(ActivityType::IMPORT_DELETE, $import);

        return redirect('/import');
    }
}
