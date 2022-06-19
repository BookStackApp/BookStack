<?php

namespace Tests\Api;

use BookStack\Entities\Models\Book;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Uploads\UsesImages;

class BooksApiTest extends TestCase
{
    use TestsApi;
    use UsesImages;

    protected string $baseEndpoint = '/api/books';

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

    public function test_update_increments_updated_date_if_only_tags_are_sent()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();
        DB::table('books')->where('id', '=', $book->id)->update(['updated_at' => Carbon::now()->subWeek()]);

        $details = [
            'tags' => [['name' => 'Category', 'value' => 'Testing']],
        ];

        $this->putJson($this->baseEndpoint . "/{$book->id}", $details);
        $book->refresh();
        $this->assertGreaterThan(Carbon::now()->subDay()->unix(), $book->updated_at->unix());
    }

    public function test_update_cover_image_control()
    {
        $this->actingAsApiEditor();
        /** @var Book $book */
        $book = Book::visible()->first();
        $this->assertNull($book->cover);
        $file = $this->getTestImage('image.png');

        // Ensure cover image can be set via API
        $resp = $this->call('PUT', $this->baseEndpoint . "/{$book->id}", [
            'name'  => 'My updated API book with image',
        ], [], ['image' => $file]);
        $book->refresh();

        $resp->assertStatus(200);
        $this->assertNotNull($book->cover);

        // Ensure further updates without image do not clear cover image
        $resp = $this->put($this->baseEndpoint . "/{$book->id}", [
            'name' => 'My updated book again'
        ]);
        $book->refresh();

        $resp->assertStatus(200);
        $this->assertNotNull($book->cover);

        // Ensure update with null image property clears image
        $resp = $this->put($this->baseEndpoint . "/{$book->id}", [
            'image' => null,
        ]);
        $book->refresh();

        $resp->assertStatus(200);
        $this->assertNull($book->cover);
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
