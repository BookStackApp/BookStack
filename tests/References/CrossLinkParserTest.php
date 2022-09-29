<?php

namespace Tests\References;

use BookStack\Entities\Models\Page;
use BookStack\References\CrossLinkParser;
use Tests\TestCase;

class CrossLinkParserTest extends TestCase
{
    public function test_instance_with_entity_resolvers_matches_entity_links()
    {
        $entities = $this->entities->all();
        $otherPage = Page::query()->where('id', '!=', $entities['page']->id)->first();

        $html = '
<a href="' . url('/link/' . $otherPage->id) . '#cat">Page Permalink</a>
<a href="' . $entities['page']->getUrl() . '?a=b">Page Link</a>
<a href="' . $entities['chapter']->getUrl() . '?cat=mouse#donkey">Chapter Link</a>
<a href="' . $entities['book']->getUrl() . '/edit">Book Link</a>
<a href="' . $entities['bookshelf']->getUrl() . '/edit?cat=happy#hello">Shelf Link</a>
<a href="' . url('/settings') . '">Settings Link</a>
        ';

        $parser = CrossLinkParser::createWithEntityResolvers();
        $results = $parser->extractLinkedModels($html);

        $this->assertCount(5, $results);
        $this->assertEquals(get_class($otherPage), get_class($results[0]));
        $this->assertEquals($otherPage->id, $results[0]->id);
        $this->assertEquals(get_class($entities['page']), get_class($results[1]));
        $this->assertEquals($entities['page']->id, $results[1]->id);
        $this->assertEquals(get_class($entities['chapter']), get_class($results[2]));
        $this->assertEquals($entities['chapter']->id, $results[2]->id);
        $this->assertEquals(get_class($entities['book']), get_class($results[3]));
        $this->assertEquals($entities['book']->id, $results[3]->id);
        $this->assertEquals(get_class($entities['bookshelf']), get_class($results[4]));
        $this->assertEquals($entities['bookshelf']->id, $results[4]->id);
    }

    public function test_similar_page_and_book_reference_links_dont_conflict()
    {
        $page = Page::query()->first();
        $book = $page->book;

        $html = '
<a href="' . $page->getUrl() . '">Page Link</a>
<a href="' . $book->getUrl() . '">Book Link</a>
        ';

        $parser = CrossLinkParser::createWithEntityResolvers();
        $results = $parser->extractLinkedModels($html);

        $this->assertCount(2, $results);
        $this->assertEquals(get_class($page), get_class($results[0]));
        $this->assertEquals($page->id, $results[0]->id);
        $this->assertEquals(get_class($book), get_class($results[1]));
        $this->assertEquals($book->id, $results[1]->id);
    }
}
