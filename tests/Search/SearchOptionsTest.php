<?php

namespace Tests\Search;

use BookStack\Search\Options\ExactSearchOption;
use BookStack\Search\Options\FilterSearchOption;
use BookStack\Search\Options\TagSearchOption;
use BookStack\Search\Options\TermSearchOption;
use BookStack\Search\SearchOptions;
use BookStack\Search\SearchOptionSet;
use Illuminate\Http\Request;
use Tests\TestCase;

class SearchOptionsTest extends TestCase
{
    public function test_from_string_parses_a_search_string_properly()
    {
        $options = SearchOptions::fromString('cat "dog" [tag=good] {is_tree}');

        $this->assertEquals(['cat'], $options->searches->toValueArray());
        $this->assertEquals(['dog'], $options->exacts->toValueArray());
        $this->assertEquals(['tag=good'], $options->tags->toValueArray());
        $this->assertEquals(['is_tree' => ''], $options->filters->toValueMap());
    }

    public function test_from_string_parses_negations()
    {
        $options = SearchOptions::fromString('cat -"dog" -[tag=good] -{is_tree}');

        $this->assertEquals(['cat'], $options->searches->toValueArray());
        $this->assertTrue($options->exacts->all()[0]->negated);
        $this->assertTrue($options->tags->all()[0]->negated);
        $this->assertTrue($options->filters->all()[0]->negated);
    }

    public function test_from_string_properly_parses_escaped_quotes()
    {
        $options = SearchOptions::fromString('"\"cat\"" surprise');
        $this->assertEquals(['"cat"'], $options->exacts->toValueArray());

        $options = SearchOptions::fromString('"\"\"" "\"donkey"');
        $this->assertEquals(['""', '"donkey'], $options->exacts->toValueArray());

        $options = SearchOptions::fromString('"\"" "\\\\"');
        $this->assertEquals(['"', '\\'], $options->exacts->toValueArray());
    }

    public function test_to_string_includes_all_items_in_the_correct_format()
    {
        $expected = 'cat "dog" [tag=good] {is_tree} {beans:valid}';
        $options = new SearchOptions();
        $options->searches = SearchOptionSet::fromValueArray(['cat'], TermSearchOption::class);
        $options->exacts = SearchOptionSet::fromValueArray(['dog'], ExactSearchOption::class);
        $options->tags = SearchOptionSet::fromValueArray(['tag=good'], TagSearchOption::class);
        $options->filters = new SearchOptionSet([
            new FilterSearchOption('', 'is_tree'),
            new FilterSearchOption('valid', 'beans'),
        ]);

        $output = $options->toString();
        foreach (explode(' ', $expected) as $term) {
            $this->assertStringContainsString($term, $output);
        }
    }

    public function test_to_string_handles_negations_as_expected()
    {
        $expected = 'cat -"dog" -[tag=good] -{is_tree}';
        $options = new SearchOptions();
        $options->searches = new SearchOptionSet([new TermSearchOption('cat')]);
        $options->exacts = new SearchOptionSet([new ExactSearchOption('dog', true)]);
        $options->tags = new SearchOptionSet([new TagSearchOption('tag=good', true)]);
        $options->filters = new SearchOptionSet([
            new FilterSearchOption('', 'is_tree', true),
        ]);

        $output = $options->toString();
        foreach (explode(' ', $expected) as $term) {
            $this->assertStringContainsString($term, $output);
        }
    }

    public function test_to_string_escapes_as_expected()
    {
        $options = new SearchOptions();
        $options->exacts = SearchOptionSet::fromValueArray(['"cat"', '""', '"donkey', '"', '\\', '\\"'], ExactSearchOption::class);

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
        ], $opts->filters->toValueMap());
    }
    public function test_it_cannot_parse_out_empty_exacts()
    {
        $options = SearchOptions::fromString('"" test ""');

        $this->assertEmpty($options->exacts->toValueArray());
        $this->assertCount(1, $options->searches->toValueArray());
    }

    public function test_from_request_properly_parses_exacts_from_search_terms()
    {
        $this->asEditor();
        $request = new Request([
            'search' => 'biscuits "cheese" "" "baked beans"'
        ]);

        $options = SearchOptions::fromRequest($request);
        $this->assertEquals(["biscuits"], $options->searches->toValueArray());
        $this->assertEquals(['"cheese"', '""', '"baked',  'beans"'], $options->exacts->toValueArray());
    }

    public function test_from_request_properly_parses_provided_types()
    {
        $request = new Request([
            'search' => '',
            'types' => ['page', 'book'],
        ]);

        $options = SearchOptions::fromRequest($request);
        $filters = $options->filters->toValueMap();
        $this->assertCount(1, $filters);
        $this->assertEquals('page|book', $filters['type'] ?? 'notfound');
    }

    public function test_from_request_properly_parses_out_extras_as_string()
    {
        $request = new Request([
            'search' => '',
            'tags' => ['a=b'],
            'extras' => '-[b=c] -{viewed_by_me} -"dino"'
        ]);

        $options = SearchOptions::fromRequest($request);
        $this->assertCount(2, $options->tags->all());
        $this->assertEquals('b=c', $options->tags->negated()->all()[0]->value);
        $this->assertEquals('viewed_by_me', $options->filters->all()[0]->getKey());
        $this->assertTrue($options->filters->all()[0]->negated);
        $this->assertEquals('dino', $options->exacts->all()[0]->value);
        $this->assertTrue($options->exacts->all()[0]->negated);
    }

    public function test_from_string_results_are_count_limited_and_larger_for_logged_in_users()
    {
        $terms = [
            ...array_fill(0, 40, 'cat'),
            ...array_fill(0, 50, '"bees"'),
            ...array_fill(0, 50, '{is_template}'),
            ...array_fill(0, 50, '[a=b]'),
        ];

        $options = SearchOptions::fromString(implode(' ', $terms));

        $this->assertCount(5, $options->searches->all());
        $this->assertCount(2, $options->exacts->all());
        $this->assertCount(4, $options->tags->all());
        $this->assertCount(5, $options->filters->all());

        $this->asEditor();
        $options = SearchOptions::fromString(implode(' ', $terms));

        $this->assertCount(10, $options->searches->all());
        $this->assertCount(4, $options->exacts->all());
        $this->assertCount(8, $options->tags->all());
        $this->assertCount(10, $options->filters->all());
    }

    public function test_from_request_results_are_count_limited_and_larger_for_logged_in_users()
    {
        $request = new Request([
            'search' => str_repeat('hello ', 20),
            'tags' => array_fill(0, 20, 'a=b'),
            'extras' => str_repeat('-[b=c] -{viewed_by_me} -"dino"', 20),
        ]);

        $options = SearchOptions::fromRequest($request);

        $this->assertCount(5, $options->searches->all());
        $this->assertCount(2, $options->exacts->all());
        $this->assertCount(4, $options->tags->all());
        $this->assertCount(5, $options->filters->all());

        $this->asEditor();
        $options = SearchOptions::fromRequest($request);

        $this->assertCount(10, $options->searches->all());
        $this->assertCount(4, $options->exacts->all());
        $this->assertCount(8, $options->tags->all());
        $this->assertCount(10, $options->filters->all());
    }
}
