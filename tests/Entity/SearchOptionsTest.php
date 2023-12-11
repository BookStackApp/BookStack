<?php

namespace Tests\Entity;

use BookStack\Search\SearchOptions;
use Illuminate\Http\Request;
use Tests\TestCase;

class SearchOptionsTest extends TestCase
{
    public function test_from_string_parses_a_search_string_properly()
    {
        $options = SearchOptions::fromString('cat "dog" [tag=good] {is_tree}');

        $this->assertEquals(['cat'], $options->searches);
        $this->assertEquals(['dog'], $options->exacts);
        $this->assertEquals(['tag=good'], $options->tags);
        $this->assertEquals(['is_tree' => ''], $options->filters);
    }

    public function test_from_string_properly_parses_escaped_quotes()
    {
        $options = SearchOptions::fromString('"\"cat\"" surprise "\"\"" "\"donkey" "\"" "\\\\"');

        $this->assertEquals(['"cat"', '""', '"donkey', '"', '\\'], $options->exacts);
    }

    public function test_to_string_includes_all_items_in_the_correct_format()
    {
        $expected = 'cat "dog" [tag=good] {is_tree}';
        $options = new SearchOptions();
        $options->searches = ['cat'];
        $options->exacts = ['dog'];
        $options->tags = ['tag=good'];
        $options->filters = ['is_tree' => ''];

        $output = $options->toString();
        foreach (explode(' ', $expected) as $term) {
            $this->assertStringContainsString($term, $output);
        }
    }

    public function test_to_string_escapes_as_expected()
    {
        $options = new SearchOptions();
        $options->exacts = ['"cat"', '""', '"donkey', '"', '\\', '\\"'];

        $output = $options->toString();
        $this->assertEquals('"\"cat\"" "\"\"" "\"donkey" "\"" "\\\\" "\\\\\""', $output);
    }

    public function test_correct_filter_values_are_set_from_string()
    {
        $opts = SearchOptions::fromString('{is_tree} {name:dan} {cat:happy}');

        $this->assertEquals([
            'is_tree' => '',
            'name'    => 'dan',
            'cat'     => 'happy',
        ], $opts->filters);
    }
    public function test_it_cannot_parse_out_empty_exacts()
    {
        $options = SearchOptions::fromString('"" test ""');

        $this->assertEmpty($options->exacts);
        $this->assertCount(1, $options->searches);
    }

    public function test_from_request_properly_parses_exacts_from_search_terms()
    {
        $request = new Request([
            'search' => 'biscuits "cheese" "" "baked beans"'
        ]);

        $options = SearchOptions::fromRequest($request);
        $this->assertEquals(["biscuits"], $options->searches);
        $this->assertEquals(['"cheese"', '""', '"baked',  'beans"'], $options->exacts);
    }
}
