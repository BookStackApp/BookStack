<?php

namespace Tests\Exports;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Exports\ZipExports\ZipImportRunner;
use BookStack\Uploads\Image;
use Tests\TestCase;

class ZipImportRunnerTest extends TestCase
{
    protected ZipImportRunner $runner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runner = app()->make(ZipImportRunner::class);
    }

    public function test_book_import()
    {
        $testImagePath = $this->files->testFilePath('test-image.png');
        $testFilePath = $this->files->testFilePath('test-file.txt');
        $import = ZipTestHelper::importFromData([], [
            'book' => [
                'id' => 5,
                'name' => 'Import test',
                'cover' => 'book_cover_image',
                'description_html' => '<p><a href="[[bsexport:page:3]]">Link to chapter page</a></p>',
                'tags' => [
                    ['name' => 'Animal', 'value' => 'Cat'],
                    ['name' => 'Category', 'value' => 'Test'],
                ],
                'chapters' => [
                    [
                        'id' => 6,
                        'name' => 'Chapter A',
                        'description_html' => '<p><a href="[[bsexport:book:5]]">Link to book</a></p>',
                        'priority' => 1,
                        'tags' => [
                            ['name' => 'Reviewed'],
                            ['name' => 'Category', 'value' => 'Test Chapter'],
                        ],
                        'pages' => [
                            [
                                'id' => 3,
                                'name' => 'Page A',
                                'priority' => 6,
                                'html' => '
<p><a href="[[bsexport:page:3]]">Link to self</a></p>
<p><a href="[[bsexport:image:1]]">Link to cat image</a></p>
<p><a href="[[bsexport:attachment:4]]">Link to text attachment</a></p>',
                                'tags' => [
                                    ['name' => 'Unreviewed'],
                                ],
                                'attachments' => [
                                    [
                                        'id' => 4,
                                        'name' => 'Text attachment',
                                        'file' => 'file_attachment'
                                    ],
                                    [
                                        'name' => 'Cats',
                                        'link' => 'https://example.com/cats',
                                    ]
                                ],
                                'images' => [
                                    [
                                        'id' => 1,
                                        'name' => 'Cat',
                                        'type' => 'gallery',
                                        'file' => 'cat_image'
                                    ],
                                    [
                                        'id' => 2,
                                        'name' => 'Dog Drawing',
                                        'type' => 'drawio',
                                        'file' => 'dog_image'
                                    ]
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Chapter child B',
                        'priority' => 5,
                    ]
                ],
                'pages' => [
                    [
                        'name' => 'Page C',
                        'markdown' => '[Link to text]([[bsexport:attachment:4]]?scale=big)',
                        'priority' => 3,
                    ]
                ],
            ],
        ], [
            'book_cover_image' => $testImagePath,
            'file_attachment'  => $testFilePath,
            'cat_image' => $testImagePath,
            'dog_image' => $testImagePath,
        ]);

        $this->asAdmin();
        /** @var Book $book */
        $book = $this->runner->run($import);

        // Book checks
        $this->assertEquals('Import test', $book->name);
        $this->assertFileExists(public_path($book->cover->path));
        $this->assertCount(2, $book->tags);
        $this->assertEquals('Cat', $book->tags()->first()->value);
        $this->assertCount(2, $book->chapters);
        $this->assertEquals(1, $book->directPages()->count());

        // Chapter checks
        $chapterA = $book->chapters()->where('name', 'Chapter A')->first();
        $this->assertCount(2, $chapterA->tags);
        $firstChapterTag = $chapterA->tags()->first();
        $this->assertEquals('Reviewed', $firstChapterTag->name);
        $this->assertEquals('', $firstChapterTag->value);
        $this->assertCount(1, $chapterA->pages);

        // Page checks
        /** @var Page $pageA */
        $pageA = $chapterA->pages->first();
        $this->assertEquals('Page A', $pageA->name);
        $this->assertCount(1, $pageA->tags);
        $firstPageTag = $pageA->tags()->first();
        $this->assertEquals('Unreviewed', $firstPageTag->name);
        $this->assertCount(2, $pageA->attachments);
        $firstAttachment = $pageA->attachments->first();
        $this->assertEquals('Text attachment', $firstAttachment->name);
        $this->assertFileEquals($testFilePath, storage_path($firstAttachment->path));
        $this->assertFalse($firstAttachment->external);
        $secondAttachment = $pageA->attachments->last();
        $this->assertEquals('Cats', $secondAttachment->name);
        $this->assertEquals('https://example.com/cats', $secondAttachment->path);
        $this->assertTrue($secondAttachment->external);
        $pageAImages = Image::where('uploaded_to', '=', $pageA->id)->whereIn('type', ['gallery', 'drawio'])->get();
        $this->assertCount(2, $pageAImages);
        $this->assertEquals('Cat', $pageAImages[0]->name);
        $this->assertEquals('gallery', $pageAImages[0]->type);
        $this->assertFileEquals($testImagePath, public_path($pageAImages[0]->path));
        $this->assertEquals('Dog Drawing', $pageAImages[1]->name);
        $this->assertEquals('drawio', $pageAImages[1]->type);

        // Book order check
        $children = $book->getDirectVisibleChildren()->values()->all();
        $this->assertEquals($children[0]->name, 'Chapter A');
        $this->assertEquals($children[1]->name, 'Page C');
        $this->assertEquals($children[2]->name, 'Chapter child B');

        // Reference checks
        $textAttachmentUrl = $firstAttachment->getUrl();
        $this->assertStringContainsString($pageA->getUrl(), $book->description_html);
        $this->assertStringContainsString($book->getUrl(), $chapterA->description_html);
        $this->assertStringContainsString($pageA->getUrl(), $pageA->html);
        $this->assertStringContainsString($pageAImages[0]->getThumb(1680, null, true), $pageA->html);
        $this->assertStringContainsString($firstAttachment->getUrl(), $pageA->html);

        // Reference in converted markdown
        $pageC = $children[1];
        $this->assertStringContainsString("href=\"{$textAttachmentUrl}?scale=big\"", $pageC->html);

        ZipTestHelper::deleteZipForImport($import);
    }

    // TODO - Test full book import
    // TODO - Test full chapter import
    // TODO - Test full page import
}
