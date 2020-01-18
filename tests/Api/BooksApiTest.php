<?php namespace Tests;

use BookStack\Entities\Book;

class BooksApiTest extends TestCase
{
    use TestsApi;

    protected $baseEndpoint = '/api/books';

    public function test_index_endpoint_returns_expected_book()
    {
        $this->actingAsApiEditor();
        $firstBook = Book::query()->orderBy('id', 'asc')->first();

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id' => $firstBook->id,
                'name' => $firstBook->name,
                'slug' => $firstBook->slug,
            ]
        ]]);
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiEditor();
        $details = [
            'name' => 'My API book',
            'description' => 'A book created via the API',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(200);
        $newItem = Book::query()->orderByDesc('id')->where('name', '=', $details['name'])->first();
        $resp->assertJson(array_merge($details, ['id' => $newItem->id, 'slug' => $newItem->slug]));
        $this->assertActivityExists('book_create', $newItem);
    }

    public function test_book_name_needed_to_create()
    {
        $this->actingAsApiEditor();
        $details = [
            'description' => 'A book created via the API',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(422);
        $resp->assertJson([
            "error" => [
                "message" => "The given data was invalid.",
                "validation" => [
                    "name" => ["The name field is required."]
                ],
                "code" => 422,
            ],
        ]);
    }

    public function test_read_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();

        $resp = $this->getJson($this->baseEndpoint . "/{$book->id}");

        $resp->assertStatus(200);
        $resp->assertJson([
            'id' => $book->id,
            'slug' => $book->slug,
            'created_by' => [
                'name' => $book->createdBy->name,
            ],
            'updated_by' => [
                'name' => $book->createdBy->name,
            ]
        ]);
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();
        $details = [
            'name' => 'My updated API book',
            'description' => 'A book created via the API',
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$book->id}", $details);
        $book->refresh();

        $resp->assertStatus(200);
        $resp->assertJson(array_merge($details, ['id' => $book->id, 'slug' => $book->slug]));
        $this->assertActivityExists('book_update', $book);
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();
        $resp = $this->deleteJson($this->baseEndpoint . "/{$book->id}");

        $resp->assertStatus(204);
        $this->assertActivityExists('book_delete');
    }
}