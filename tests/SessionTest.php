<?php

namespace Tests;

class SessionTest extends TestCase
{
    public function test_secure_images_not_tracked_in_session_history()
    {
        config()->set('filesystems.images', 'local_secure');
        $this->asEditor();
        $page = $this->entities->page();
        $result = $this->files->uploadGalleryImageToPage($this, $page);
        $expectedPath = storage_path($result['path']);
        $this->assertFileExists($expectedPath);

        $this->get('/books');
        $this->assertEquals(url('/books'), session()->previousUrl());

        $resp = $this->get($result['path']);
        $resp->assertOk();
        $resp->assertHeader('Content-Type', 'image/png');

        $this->assertEquals(url('/books'), session()->previousUrl());

        if (file_exists($expectedPath)) {
            unlink($expectedPath);
        }
    }

    public function test_pwa_manifest_is_not_tracked_in_session_history()
    {
        $this->asEditor()->get('/books');
        $this->get('/manifest.json');

        $this->assertEquals(url('/books'), session()->previousUrl());
    }

    public function test_dist_dir_access_is_not_tracked_in_session_history()
    {
        $this->asEditor()->get('/books');
        $this->get('/dist/sub/hello.txt');

        $this->assertEquals(url('/books'), session()->previousUrl());
    }

    public function test_opensearch_is_not_tracked_in_session_history()
    {
        $this->asEditor()->get('/books');
        $this->get('/opensearch.xml');

        $this->assertEquals(url('/books'), session()->previousUrl());
    }
}
