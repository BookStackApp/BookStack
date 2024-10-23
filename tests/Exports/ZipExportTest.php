<?php

namespace Tests\Exports;

use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use ZipArchive;

class ZipExportTest extends TestCase
{
    public function test_export_results_in_zip_format()
    {
        $page = $this->entities->page();
        $response = $this->asEditor()->get($page->getUrl("/export/zip"));

        $zipData = $response->streamedContent();
        $zipFile = tempnam(sys_get_temp_dir(), 'bstesta-');
        file_put_contents($zipFile, $zipData);
        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::RDONLY);

        $this->assertNotFalse($zip->locateName('data.json'));
        $this->assertNotFalse($zip->locateName('files/'));

        $data = json_decode($zip->getFromName('data.json'), true);
        $this->assertIsArray($data);
        $this->assertGreaterThan(0, count($data));

        $zip->close();
        unlink($zipFile);
    }

    public function test_export_metadata()
    {
        $page = $this->entities->page();
        $zipResp = $this->asEditor()->get($page->getUrl("/export/zip"));
        $zip = $this->extractZipResponse($zipResp);

        $this->assertEquals($page->id, $zip->data['page']['id'] ?? null);
        $this->assertArrayNotHasKey('book', $zip->data);
        $this->assertArrayNotHasKey('chapter', $zip->data);

        $now = time();
        $date = Carbon::parse($zip->data['exported_at'])->unix();
        $this->assertLessThan($now + 2, $date);
        $this->assertGreaterThan($now - 2, $date);

        $version = trim(file_get_contents(base_path('version')));
        $this->assertEquals($version, $zip->data['instance']['version']);

        $instanceId = decrypt($zip->data['instance']['id_ciphertext']);
        $this->assertEquals('bookstack', $instanceId);
    }

    public function test_page_export()
    {
        // TODO
    }

    public function test_book_export()
    {
        // TODO
    }

    public function test_chapter_export()
    {
        // TODO
    }

    protected function extractZipResponse(TestResponse $response): ZipResultData
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
