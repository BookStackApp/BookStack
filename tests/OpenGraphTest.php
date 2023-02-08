<?php

namespace Tests;

use BookStack\Entities\Repos\BaseRepo;
use BookStack\Entities\Repos\BookRepo;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

class OpenGraphTest extends TestCase
{
    public function test_page_tags()
    {
        $page = $this->entities->page();
        $resp = $this->asEditor()->get($page->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($page->getShortName() . ' | BookStack', $tags['title']);
        $this->assertEquals($page->getUrl(), $tags['url']);
        $this->assertEquals(Str::limit($page->text, 100, '...'), $tags['description']);
    }

    public function test_chapter_tags()
    {
        $chapter = $this->entities->chapter();
        $resp = $this->asEditor()->get($chapter->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($chapter->getShortName() . ' | BookStack', $tags['title']);
        $this->assertEquals($chapter->getUrl(), $tags['url']);
        $this->assertEquals(Str::limit($chapter->description, 100, '...'), $tags['description']);
    }

    public function test_book_tags()
    {
        $book = $this->entities->book();
        $resp = $this->asEditor()->get($book->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($book->getShortName() . ' | BookStack', $tags['title']);
        $this->assertEquals($book->getUrl(), $tags['url']);
        $this->assertEquals(Str::limit($book->description, 100, '...'), $tags['description']);
        $this->assertArrayNotHasKey('image', $tags);

        // Test image set if image has cover image
        $bookRepo = app(BookRepo::class);
        $bookRepo->updateCoverImage($book, $this->files->uploadedImage('image.png'));
        $resp = $this->asEditor()->get($book->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($book->getBookCover(), $tags['image']);
    }

    public function test_shelf_tags()
    {
        $shelf = $this->entities->shelf();
        $resp = $this->asEditor()->get($shelf->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($shelf->getShortName() . ' | BookStack', $tags['title']);
        $this->assertEquals($shelf->getUrl(), $tags['url']);
        $this->assertEquals(Str::limit($shelf->description, 100, '...'), $tags['description']);
        $this->assertArrayNotHasKey('image', $tags);

        // Test image set if image has cover image
        $baseRepo = app(BaseRepo::class);
        $baseRepo->updateCoverImage($shelf, $this->files->uploadedImage('image.png'));
        $resp = $this->asEditor()->get($shelf->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($shelf->getBookCover(), $tags['image']);
    }

    /**
     * Parse the open graph tags from a test response.
     */
    protected function getOpenGraphTags(TestResponse $resp): array
    {
        $tags = [];

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($resp->getContent());
        $metaElems = $doc->getElementsByTagName('meta');
        /** @var \DOMElement $elem */
        foreach ($metaElems as $elem) {
            $prop = $elem->getAttribute('property');
            $name = explode(':', $prop)[1] ?? null;
            if ($name) {
                $tags[$name] = $elem->getAttribute('content');
            }
        }

        return $tags;
    }
}
