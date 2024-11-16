<?php

namespace Tests\Exports;

use BookStack\Exports\Import;
use Illuminate\Http\UploadedFile;
use ZipArchive;

class ZipTestHelper
{
    public static function importFromData(array $importData, array $zipData): Import
    {
        if (isset($zipData['book'])) {
            $importData['type'] = 'book';
        } else if (isset($zipData['chapter'])) {
            $importData['type'] = 'chapter';
        } else if (isset($zipData['page'])) {
            $importData['type'] = 'page';
        }

        $import = Import::factory()->create($importData);
        $zip = static::zipUploadFromData($zipData);
        rename($zip->getRealPath(), storage_path($import->path));

        return $import;
    }

    public static function deleteZipForImport(Import $import): void
    {
        $path = storage_path($import->path);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public static function zipUploadFromData(array $data): UploadedFile
    {
        $zipFile = tempnam(sys_get_temp_dir(), 'bstest-');

        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::CREATE);
        $zip->addFromString('data.json', json_encode($data));
        $zip->close();

        return new UploadedFile($zipFile, 'upload.zip', 'application/zip', null, true);
    }
}
