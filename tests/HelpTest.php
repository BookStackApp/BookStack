<?php

namespace Tests;

class HelpTest extends TestCase
{
    public function test_wysiwyg_help_shows_tiny_and_tiny_license_link()
    {
        $resp = $this->get('/help/wysiwyg');
        $resp->assertOk();
        $this->withHtml($resp)->assertElementExists('a[href="https://www.tiny.cloud/"]');
        $this->withHtml($resp)->assertElementExists('a[href="' . url('/libs/tinymce/license.txt') . '"]');
    }

    public function test_tiny_license_exists_where_expected()
    {
        $expectedPath = public_path('/libs/tinymce/license.txt');
        $this->assertTrue(file_exists($expectedPath));

        $contents = file_get_contents($expectedPath);
        $this->assertStringContainsString('MIT License', $contents);
    }
}
