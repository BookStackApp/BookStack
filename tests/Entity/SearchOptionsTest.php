<?php

namespace Tests\Entity;

use BookStack\Entities\Tools\SearchOptions;
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

    public function test_correct_filter_values_are_set_from_string()
    {
        $opts = SearchOptions::fromString('{is_tree} {name:dan} {cat:happy}');

        $this->assertEquals([
            'is_tree' => '',
            'name'    => 'dan',
            'cat'     => 'happy',
        ], $opts->filters);
    }
}
