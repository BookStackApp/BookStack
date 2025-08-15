<?php

namespace Tests\Exports;

use BookStack\Exports\Import;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use ZipArchive;

class ZipTestHelper
{
    public static function importFromData(array $importData, array $zipData, array $files = []): Import
    {
        if (isset($zipData['book'])) {
            $importData['type'] = 'book';
        } else if (isset($zipData['chapter'])) {
            $importData['type'] = 'chapter';
        } else if (isset($zipData['page'])) {
            $importData['type'] = 'page';
        }

        $import = Import::factory()->create($importData);
        $zip = static::zipUploadFromData($zipData, $files);
        $targetPath = storage_path($import->path);
        $targetDir = dirname($targetPath);

        if (!file_exists($targetDir)) {
            mkdir($targetDir);
        }

        rename($zip->getRealPath(), $targetPath);

        return $import;
    }

    public static function deleteZipForImport(Import $import): void
    {
        $path = storage_path($import->path);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public static function zipUploadFromData(array $data, array $files = []): UploadedFile
    {
        $zipFile = tempnam(sys_get_temp_dir(), 'bstest-');

        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::OVERWRITE);
        $zip->addFromString('data.json', json_encode($data));

        foreach ($files as $name => $file) {
            $zip->addFile($file, "files/$name");
        }

        $zip->close();

        return new UploadedFile($zipFile, 'upload.zip', 'application/zip', null, true);
    }

    public static function extractFromZipResponse(TestResponse $response): ZipResultData
    {
        $zipData = $response->streamedContent();
        $zipFile = tempnam(sys_get_temp_dir(), 'bstest-');

        file_put_contents($zipFile, $zipData);
        $extractDir = tempnam(sys_get_temp_dir(), 'bstestextracted-');
        if (file_exists($extractDir)) {
            unlink($extractDir);
        }
        mkdir($extractDir);

        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::RDONLY);
        $zip->extractTo($extractDir);

        $dataJson = file_get_contents($extractDir . DIRECTORY_SEPARATOR . "data.json");
        $data = json_decode($dataJson, true);

        return new ZipResultData(
            $zipFile,
            $extractDir,
            $data,
        );
    }
}
