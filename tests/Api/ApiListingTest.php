<?php namespace Tests\Api;

use BookStack\Entities\Book;
use Tests\TestCase;

class ApiListingTest extends TestCase
{
    use TestsApi;

    protected $endpoint = '/api/books';

    public function test_count_parameter_limits_responses()
    {
        $this->actingAsApiEditor();
        $bookCount = min(Book::visible()->count(), 100);

        $resp = $this->get($this->endpoint);
        $resp->assertJsonCount($bookCount, 'data');

        $resp = $this->get($this->endpoint . '?count=1');
        $resp->assertJsonCount(1, 'data');
    }

    public function test_offset_parameter()
    {
        $this->actingAsApiEditor();
        $books = Book::visible()->orderBy('id')->take(3)->get();

        $resp = $this->get($this->endpoint . '?count=1');
        $resp->assertJsonMissing(['name' => $books[1]->name ]);

        $resp = $this->get($this->endpoint . '?count=1&offset=1000');
        $resp->assertJsonCount(0, 'data');
    }

    public function test_sort_parameter()
    {
        $this->actingAsApiEditor();

        $sortChecks = [
            '-id' => Book::visible()->orderBy('id', 'desc')->first(),
            '+name' => Book::visible()->orderBy('name', 'asc')->first(),
            'name' => Book::visible()->orderBy('name', 'asc')->first(),
            '-name' => Book::visible()->orderBy('name', 'desc')->first()
        ];

        foreach ($sortChecks as $sortOption => $result) {
            $resp = $this->get($this->endpoint . '?count=1&sort=' . $sortOption);
            $resp->assertJson(['data' => [
                [
                    'id' => $result->id,
                    'name' => $result->name,
                ]
            ]]);
        }
    }

    public function test_filter_parameter()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();
        $nameSubstr = substr($book->name, 0, 4);
        $encodedNameSubstr = rawurlencode($nameSubstr);

        $filterChecks = [
            // Test different types of filter
            "filter[id]={$book->id}" => 1,
            "filter[id:ne]={$book->id}" => Book::visible()->where('id', '!=', $book->id)->count(),
            "filter[id:gt]={$book->id}" => Book::visible()->where('id', '>', $book->id)->count(),
            "filter[id:gte]={$book->id}" => Book::visible()->where('id', '>=', $book->id)->count(),
            "filter[id:lt]={$book->id}" => Book::visible()->where('id', '<', $book->id)->count(),
            "filter[name:like]={$encodedNameSubstr}%" => Book::visible()->where('name', 'like', $nameSubstr . '%')->count(),

            // Test mulitple filters 'and' together
            "filter[id]={$book->id}&filter[name]=random_non_existing_string" => 0,
        ];

        foreach ($filterChecks as $filterOption => $resultCount) {
            $resp = $this->get($this->endpoint . '?count=1&' . $filterOption);
            $resp->assertJson(['total' => $resultCount]);
        }
    }

}