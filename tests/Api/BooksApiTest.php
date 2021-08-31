<?php

namespace Tests\Api;

use BookStack\Entities\Models\Book;
use Tests\TestCase;

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
                'id'   => $firstBook->id,
                'name' => $firstBook->name,
                'slug' => $firstBook->slug,
            ],
        ]]);
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiEditor();
        $details = [
            'name'        => 'My API book',
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
            'error' => [
                'message'    => 'The given data was invalid.',
                'validation' => [
                    'name' => ['The name field is required.'],
                ],
                'code' => 422,
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
            'id'         => $book->id,
            'slug'       => $book->slug,
            'created_by' => [
                'name' => $book->createdBy->name,
            ],
            'updated_by' => [
                'name' => $book->createdBy->name,
            ],
            'owned_by' => [
                'name' => $book->ownedBy->name,
            ],
        ]);
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();
        $details = [
            'name'        => 'My updated API book',
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

    public function test_export_html_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$book->id}/export/html");
        $resp->assertStatus(200);
        $resp->assertSee($book->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.html"');
    }

    public function test_export_plain_text_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$book->id}/export/plaintext");
        $resp->assertStatus(200);
        $resp->assertSee($book->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.txt"');
    }

    public function test_export_pdf_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$book->id}/export/pdf");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.pdf"');
    }

    public function test_export_markdown_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->has('pages')->has('chapters')->first();

        $resp = $this->get($this->baseEndpoint . "/{$book->id}/export/markdown");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.md"');
        $resp->assertSee('# ' . $book->name);
        $resp->assertSee('# ' . $book->pages()->first()->name);
        $resp->assertSee('# ' . $book->chapters()->first()->name);
    }

    public function test_cant_export_when_not_have_permission()
    {
        $types = ['html', 'plaintext', 'pdf', 'markdown'];
        $this->actingAsApiEditor();
        $this->removePermissionFromUser($this->getEditor(), 'content-export');

        $book = Book::visible()->first();
        foreach ($types as $type) {
            $resp = $this->get($this->baseEndpoint . "/{$book->id}/export/{$type}");
            $this->assertPermissionError($resp);
        }
    }
}
