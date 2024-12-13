<?php

namespace Tests\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Repos\BaseRepo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ShelvesApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/shelves';

    public function test_index_endpoint_returns_expected_shelf()
    {
        $this->actingAsApiEditor();
        $firstBookshelf = Bookshelf::query()->orderBy('id', 'asc')->first();

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id'   => $firstBookshelf->id,
                'name' => $firstBookshelf->name,
                'slug' => $firstBookshelf->slug,
                'owned_by' => $firstBookshelf->owned_by,
                'created_by' => $firstBookshelf->created_by,
                'updated_by' => $firstBookshelf->updated_by,
                'cover' => null,
            ],
        ]]);
    }

    public function test_index_endpoint_includes_cover_if_set()
    {
        $this->actingAsApiEditor();
        $shelf = $this->entities->shelf();

        $baseRepo = $this->app->make(BaseRepo::class);
        $image = $this->files->uploadedImage('shelf_cover');
        $baseRepo->updateCoverImage($shelf, $image);

        $resp = $this->getJson($this->baseEndpoint . '?filter[id]=' . $shelf->id);
        $resp->assertJson(['data' => [
            [
                'id'   => $shelf->id,
                'cover' => [
                    'id' => $shelf->cover->id,
                    'url' => $shelf->cover->url,
                ],
            ],
        ]]);
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiEditor();
        $books = Book::query()->take(2)->get();

        $details = [
            'name'        => 'My API shelf',
            'description' => 'A shelf created via the API',
        ];

        $resp = $this->postJson($this->baseEndpoint, array_merge($details, ['books' => [$books[0]->id, $books[1]->id]]));
        $resp->assertStatus(200);
        $newItem = Bookshelf::query()->orderByDesc('id')->where('name', '=', $details['name'])->first();
        $resp->assertJson(array_merge($details, [
            'id' => $newItem->id,
            'slug' => $newItem->slug,
            'description_html' => '<p>A shelf created via the API</p>',
        ]));
        $this->assertActivityExists('bookshelf_create', $newItem);
        foreach ($books as $index => $book) {
            $this->assertDatabaseHas('bookshelves_books', [
                'bookshelf_id' => $newItem->id,
                'book_id'      => $book->id,
                'order'        => $index,
            ]);
        }
    }

    public function test_create_endpoint_with_html()
    {
        $this->actingAsApiEditor();

        $details = [
            'name'             => 'My API shelf',
            'description_html' => '<p>A <strong>shelf</strong> created via the API</p>',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(200);
        $newItem = Bookshelf::query()->orderByDesc('id')->where('name', '=', $details['name'])->first();

        $expectedDetails = array_merge($details, [
            'id'          => $newItem->id,
            'description' => 'A shelf created via the API',
        ]);

        $resp->assertJson($expectedDetails);
        $this->assertDatabaseHas('bookshelves', $expectedDetails);
    }

    public function test_shelf_name_needed_to_create()
    {
        $this->actingAsApiEditor();
        $details = [
            'description' => 'A shelf created via the API',
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
        $shelf = Bookshelf::visible()->first();

        $resp = $this->getJson($this->baseEndpoint . "/{$shelf->id}");

        $resp->assertStatus(200);
        $resp->assertJson([
            'id'         => $shelf->id,
            'slug'       => $shelf->slug,
            'created_by' => [
                'name' => $shelf->createdBy->name,
            ],
            'updated_by' => [
                'name' => $shelf->createdBy->name,
            ],
            'owned_by' => [
                'name' => $shelf->ownedBy->name,
            ],
        ]);
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiEditor();
        $shelf = Bookshelf::visible()->first();
        $details = [
            'name'        => 'My updated API shelf',
            'description' => 'A shelf updated via the API',
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$shelf->id}", $details);
        $shelf->refresh();

        $resp->assertStatus(200);
        $resp->assertJson(array_merge($details, [
            'id' => $shelf->id,
            'slug' => $shelf->slug,
            'description_html' => '<p>A shelf updated via the API</p>',
        ]));
        $this->assertActivityExists('bookshelf_update', $shelf);
    }

    public function test_update_endpoint_with_html()
    {
        $this->actingAsApiEditor();
        $shelf = Bookshelf::visible()->first();
        $details = [
            'name'             => 'My updated API shelf',
            'description_html' => '<p>A shelf <em>updated</em> via the API</p>',
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$shelf->id}", $details);
        $resp->assertStatus(200);

        $this->assertDatabaseHas('bookshelves', array_merge($details, ['id' => $shelf->id, 'description' => 'A shelf updated via the API']));
    }

    public function test_update_increments_updated_date_if_only_tags_are_sent()
    {
        $this->actingAsApiEditor();
        $shelf = Bookshelf::visible()->first();
        DB::table('bookshelves')->where('id', '=', $shelf->id)->update(['updated_at' => Carbon::now()->subWeek()]);

        $details = [
            'tags' => [['name' => 'Category', 'value' => 'Testing']],
        ];

        $this->putJson($this->baseEndpoint . "/{$shelf->id}", $details);
        $shelf->refresh();
        $this->assertGreaterThan(Carbon::now()->subDay()->unix(), $shelf->updated_at->unix());
    }

    public function test_update_only_assigns_books_if_param_provided()
    {
        $this->actingAsApiEditor();
        $shelf = Bookshelf::visible()->first();
        $this->assertTrue($shelf->books()->count() > 0);
        $details = [
            'name' => 'My updated API shelf',
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$shelf->id}", $details);
        $resp->assertStatus(200);
        $this->assertTrue($shelf->books()->count() > 0);

        $resp = $this->putJson($this->baseEndpoint . "/{$shelf->id}", ['books' => []]);
        $resp->assertStatus(200);
        $this->assertTrue($shelf->books()->count() === 0);
    }

    public function test_update_cover_image_control()
    {
        $this->actingAsApiEditor();
        /** @var Book $shelf */
        $shelf = Bookshelf::visible()->first();
        $this->assertNull($shelf->cover);
        $file = $this->files->uploadedImage('image.png');

        // Ensure cover image can be set via API
        $resp = $this->call('PUT', $this->baseEndpoint . "/{$shelf->id}", [
            'name'  => 'My updated API shelf with image',
        ], [], ['image' => $file]);
        $shelf->refresh();

        $resp->assertStatus(200);
        $this->assertNotNull($shelf->cover);

        // Ensure further updates without image do not clear cover image
        $resp = $this->put($this->baseEndpoint . "/{$shelf->id}", [
            'name' => 'My updated shelf again',
        ]);
        $shelf->refresh();

        $resp->assertStatus(200);
        $this->assertNotNull($shelf->cover);

        // Ensure update with null image property clears image
        $resp = $this->put($this->baseEndpoint . "/{$shelf->id}", [
            'image' => null,
        ]);
        $shelf->refresh();

        $resp->assertStatus(200);
        $this->assertNull($shelf->cover);
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiEditor();
        $shelf = Bookshelf::visible()->first();
        $resp = $this->deleteJson($this->baseEndpoint . "/{$shelf->id}");

        $resp->assertStatus(204);
        $this->assertActivityExists('bookshelf_delete');
    }
}
