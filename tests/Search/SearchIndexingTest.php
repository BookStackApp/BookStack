<?php

namespace Search;

use Tests\TestCase;

class SearchIndexingTest extends TestCase
{
    public function test_terms_in_headers_have_an_adjusted_index_score()
    {
        $page = $this->entities->newPage(['name' => 'Test page A', 'html' => '
            <p>TermA</p>
            <h1>TermB <strong>TermNested</strong></h1>
            <h2>TermC</h2>
            <h3>TermD</h3>
            <h4>TermE</h4>
            <h5>TermF</h5>
            <h6>TermG</h6>
        ']);

        $scoreByTerm = $page->searchTerms()->pluck('score', 'term');

        $this->assertEquals(1, $scoreByTerm->get('TermA'));
        $this->assertEquals(10, $scoreByTerm->get('TermB'));
        $this->assertEquals(10, $scoreByTerm->get('TermNested'));
        $this->assertEquals(5, $scoreByTerm->get('TermC'));
        $this->assertEquals(4, $scoreByTerm->get('TermD'));
        $this->assertEquals(3, $scoreByTerm->get('TermE'));
        $this->assertEquals(2, $scoreByTerm->get('TermF'));
        // Is 1.5 but stored as integer, rounding up
        $this->assertEquals(2, $scoreByTerm->get('TermG'));
    }

    public function test_indexing_works_as_expected_for_page_with_lots_of_terms()
    {
        $this->markTestSkipped('Time consuming test');

        $count = 100000;
        $text = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_#';
        for ($i = 0; $i < $count; $i++) {
            $text .= substr(str_shuffle($chars), 0, 5) . ' ';
        }

        $page = $this->entities->newPage(['name' => 'Test page A', 'html' => '<p>' . $text . '</p>']);

        $termCount = $page->searchTerms()->count();

        // Expect at least 90% unique rate
        $this->assertGreaterThan($count * 0.9, $termCount);
    }

    public function test_name_and_content_terms_are_merged_to_single_score()
    {
        $page = $this->entities->newPage(['name' => 'TermA', 'html' => '
            <p>TermA</p>
        ']);

        $scoreByTerm = $page->searchTerms()->pluck('score', 'term');

        // Scores 40 for being in the name then 1 for being in the content
        $this->assertEquals(41, $scoreByTerm->get('TermA'));
    }

    public function test_tag_names_and_values_are_indexed_for_search()
    {
        $page = $this->entities->newPage(['name' => 'PageA', 'html' => '<p>content</p>', 'tags' => [
            ['name' => 'Animal', 'value' => 'MeowieCat'],
            ['name' => 'SuperImportant'],
        ]]);

        $scoreByTerm = $page->searchTerms()->pluck('score', 'term');
        $this->assertEquals(5, $scoreByTerm->get('MeowieCat'));
        $this->assertEquals(3, $scoreByTerm->get('Animal'));
        $this->assertEquals(3, $scoreByTerm->get('SuperImportant'));
    }
}
