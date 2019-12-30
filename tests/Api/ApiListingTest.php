<?php

namespace Tests;

use BookStack\Auth\Permissions\RolePermission;
use BookStack\Entities\Book;
use Carbon\Carbon;

class ApiAuthTest extends TestCase
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

}