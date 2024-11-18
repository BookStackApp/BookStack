<?php

namespace Tests\Exports;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Exports\ZipExports\ZipExportReader;
use BookStack\Exports\ZipExports\ZipExportValidator;
use BookStack\Exports\ZipExports\ZipImportRunner;
use BookStack\Uploads\Image;
use Tests\TestCase;

class ZipExportValidatorTests extends TestCase
{
    protected array $filesToRemove = [];

    protected function tearDown(): void
    {
        foreach ($this->filesToRemove as $file) {
            unlink($file);
        }

        parent::tearDown();
    }

    protected function getValidatorForData(array $zipData, array $files = []): ZipExportValidator
    {
        $upload = ZipTestHelper::zipUploadFromData($zipData, $files);
        $path = $upload->getRealPath();
        $this->filesToRemove[] = $path;
        $reader = new ZipExportReader($path);
        return new ZipExportValidator($reader);
    }

    public function test_ids_have_to_be_unique()
    {
        $validator = $this->getValidatorForData([
            'book' => [
                'id' => 4,
                'name' => 'My book',
                'pages' => [
                    [
                        'id' => 4,
                        'name' => 'My page',
                        'markdown' => 'hello',
                        'attachments' => [
                            ['id' => 4, 'name' => 'Attachment A', 'link' => 'https://example.com'],
                            ['id' => 4, 'name' => 'Attachment B', 'link' => 'https://example.com']
                        ],
                        'images' => [
                            ['id' => 4, 'name' => 'Image A', 'type' => 'gallery', 'file' => 'cat'],
                            ['id' => 4, 'name' => 'Image b', 'type' => 'gallery', 'file' => 'cat'],
                        ],
                    ],
                    ['id' => 4, 'name' => 'My page', 'markdown' => 'hello'],
                ],
                'chapters' => [
                    ['id' => 4, 'name' => 'Chapter 1'],
                    ['id' => 4, 'name' => 'Chapter 2']
                ]
            ]
        ], ['cat' => $this->files->testFilePath('test-image.png')]);

        $results = $validator->validate();
        $this->assertCount(4, $results);

        $expectedMessage = 'The id must be unique for the object type within the ZIP.';
        $this->assertEquals($expectedMessage, $results['book.pages.0.attachments.1.id']);
        $this->assertEquals($expectedMessage, $results['book.pages.0.images.1.id']);
        $this->assertEquals($expectedMessage, $results['book.pages.1.id']);
        $this->assertEquals($expectedMessage, $results['book.chapters.1.id']);
    }
}
