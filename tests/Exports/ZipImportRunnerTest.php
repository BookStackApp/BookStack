<?php

namespace Tests\Exports;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
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

    public function test_chapter_import()
    {
        $testImagePath = $this->files->testFilePath('test-image.png');
        $testFilePath = $this->files->testFilePath('test-file.txt');
        $parent = $this->entities->book();

        $import = ZipTestHelper::importFromData([], [
            'chapter' => [
                'id' => 6,
                'name' => 'Chapter A',
                'description_html' => '<p><a href="[[bsexport:page:3]]">Link to page</a></p>',
                'priority' => 1,
                'tags' => [
                    ['name' => 'Reviewed', 'value' => '2024'],
                ],
                'pages' => [
                    [
                        'id' => 3,
                        'name' => 'Page A',
                        'priority' => 6,
                        'html' => '<p><a href="[[bsexport:chapter:6]]">Link to chapter</a></p>
<p><a href="[[bsexport:image:2]]">Link to dog drawing</a></p>
<p><a href="[[bsexport:attachment:4]]">Link to text attachment</a></p>',
                        'tags' => [
                            ['name' => 'Unreviewed'],
                        ],
                        'attachments' => [
                            [
                                'id' => 4,
                                'name' => 'Text attachment',
                                'file' => 'file_attachment'
                            ]
                        ],
                        'images' => [
                            [
                                'id' => 2,
                                'name' => 'Dog Drawing',
                                'type' => 'drawio',
                                'file' => 'dog_image'
                            ]
                        ],
                    ],
                    [
                        'name' => 'Page B',
                        'markdown' => '[Link to page A]([[bsexport:page:3]])',
                        'priority' => 9,
                    ],
                ],
            ],
        ], [
            'file_attachment'  => $testFilePath,
            'dog_image' => $testImagePath,
        ]);

        $this->asAdmin();
        /** @var Chapter $chapter */
        $chapter = $this->runner->run($import, $parent);

        // Chapter checks
        $this->assertEquals('Chapter A', $chapter->name);
        $this->assertEquals($parent->id, $chapter->book_id);
        $this->assertCount(1, $chapter->tags);
        $firstChapterTag = $chapter->tags()->first();
        $this->assertEquals('Reviewed', $firstChapterTag->name);
        $this->assertEquals('2024', $firstChapterTag->value);
        $this->assertCount(2, $chapter->pages);

        // Page checks
        /** @var Page $pageA */
        $pageA = $chapter->pages->first();
        $this->assertEquals('Page A', $pageA->name);
        $this->assertCount(1, $pageA->tags);
        $this->assertCount(1, $pageA->attachments);
        $pageAImages = Image::where('uploaded_to', '=', $pageA->id)->whereIn('type', ['gallery', 'drawio'])->get();
        $this->assertCount(1, $pageAImages);

        // Reference checks
        $attachment = $pageA->attachments->first();
        $this->assertStringContainsString($pageA->getUrl(), $chapter->description_html);
        $this->assertStringContainsString($chapter->getUrl(), $pageA->html);
        $this->assertStringContainsString($pageAImages[0]->url, $pageA->html);
        $this->assertStringContainsString($attachment->getUrl(), $pageA->html);

        ZipTestHelper::deleteZipForImport($import);
    }

    public function test_page_import()
    {
        $testImagePath = $this->files->testFilePath('test-image.png');
        $testFilePath = $this->files->testFilePath('test-file.txt');
        $parent = $this->entities->chapter();

        $import = ZipTestHelper::importFromData([], [
            'page' => [
                'id' => 3,
                'name' => 'Page A',
                'priority' => 6,
                'html' => '<p><a href="[[bsexport:page:3]]">Link to self</a></p>
<p><a href="[[bsexport:image:2]]">Link to dog drawing</a></p>
<p><a href="[[bsexport:attachment:4]]">Link to text attachment</a></p>',
                'tags' => [
                    ['name' => 'Unreviewed'],
                ],
                'attachments' => [
                    [
                        'id' => 4,
                        'name' => 'Text attachment',
                        'file' => 'file_attachment'
                    ]
                ],
                'images' => [
                    [
                        'id' => 2,
                        'name' => 'Dog Drawing',
                        'type' => 'drawio',
                        'file' => 'dog_image'
                    ]
                ],
            ],
        ], [
            'file_attachment'  => $testFilePath,
            'dog_image' => $testImagePath,
        ]);

        $this->asAdmin();
        /** @var Page $page */
        $page = $this->runner->run($import, $parent);

        // Page checks
        $this->assertEquals('Page A', $page->name);
        $this->assertCount(1, $page->tags);
        $this->assertCount(1, $page->attachments);
        $pageImages = Image::where('uploaded_to', '=', $page->id)->whereIn('type', ['gallery', 'drawio'])->get();
        $this->assertCount(1, $pageImages);
        $this->assertFileEquals($testImagePath, public_path($pageImages[0]->path));

        // Reference checks
        $this->assertStringContainsString($page->getUrl(), $page->html);
        $this->assertStringContainsString($pageImages[0]->url, $page->html);
        $this->assertStringContainsString($page->attachments->first()->getUrl(), $page->html);

        ZipTestHelper::deleteZipForImport($import);
    }

    public function test_revert_cleans_up_uploaded_files()
    {
        $testImagePath = $this->files->testFilePath('test-image.png');
        $testFilePath = $this->files->testFilePath('test-file.txt');
        $parent = $this->entities->chapter();

        $import = ZipTestHelper::importFromData([], [
            'page' => [
                'name' => 'Page A',
                'html' => '<p>Hello</p>',
                'attachments' => [
                    [
                        'name' => 'Text attachment',
                        'file' => 'file_attachment'
                    ]
                ],
                'images' => [
                    [
                        'name' => 'Dog Image',
                        'type' => 'gallery',
                        'file' => 'dog_image'
                    ]
                ],
            ],
        ], [
            'file_attachment'  => $testFilePath,
            'dog_image' => $testImagePath,
        ]);

        $this->asAdmin();
        /** @var Page $page */
        $page = $this->runner->run($import, $parent);

        $attachment = $page->attachments->first();
        $image = Image::query()->where('uploaded_to', '=', $page->id)->where('type', '=', 'gallery')->first();

        $this->assertFileExists(public_path($image->path));
        $this->assertFileExists(storage_path($attachment->path));

        $this->runner->revertStoredFiles();

        $this->assertFileDoesNotExist(public_path($image->path));
        $this->assertFileDoesNotExist(storage_path($attachment->path));

        ZipTestHelper::deleteZipForImport($import);
    }

    public function test_imported_images_have_their_detected_extension_added()
    {
        $testImagePath = $this->files->testFilePath('test-image.png');
        $parent = $this->entities->chapter();

        $import = ZipTestHelper::importFromData([], [
            'page' => [
                'name' => 'Page A',
                'html' => '<p>hello</p>',
                'images' => [
                    [
                        'id' => 2,
                        'name' => 'Cat',
                        'type' => 'gallery',
                        'file' => 'cat_image'
                    ]
                ],
            ],
        ], [
            'cat_image' => $testImagePath,
        ]);

        $this->asAdmin();
        /** @var Page $page */
        $page = $this->runner->run($import, $parent);

        $pageImages = Image::where('uploaded_to', '=', $page->id)->whereIn('type', ['gallery', 'drawio'])->get();

        $this->assertCount(1, $pageImages);
        $this->assertStringEndsWith('.png', $pageImages[0]->url);
        $this->assertStringEndsWith('.png', $pageImages[0]->path);

        ZipTestHelper::deleteZipForImport($import);
    }

    public function test_drawing_references_are_updated_within_content()
    {
        $testImagePath = $this->files->testFilePath('test-image.png');
        $parent = $this->entities->chapter();

        $import = ZipTestHelper::importFromData([], [
            'page' => [
                'name' => 'Page A',
                'html' => '<div drawio-diagram="1125"><img src="[[bsexport:image:1125]]"></div>',
                'images' => [
                    [
                        'id' => 1125,
                        'name' => 'Cat',
                        'type' => 'drawio',
                        'file' => 'my_drawing'
                    ]
                ],
            ],
        ], [
            'my_drawing' => $testImagePath,
        ]);

        $this->asAdmin();
        /** @var Page $page */
        $page = $this->runner->run($import, $parent);

        $pageImages = Image::where('uploaded_to', '=', $page->id)->whereIn('type', ['gallery', 'drawio'])->get();
        $this->assertCount(1, $pageImages);
        $this->assertEquals('drawio', $pageImages[0]->type);

        $drawingId = $pageImages[0]->id;
        $this->assertStringContainsString("drawio-diagram=\"{$drawingId}\"", $page->html);
        $this->assertStringNotContainsString('[[bsexport:image:1125]]', $page->html);
        $this->assertStringNotContainsString('drawio-diagram="1125"', $page->html);

        ZipTestHelper::deleteZipForImport($import);
    }
}
