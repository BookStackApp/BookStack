<?php

namespace Tests\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ChaptersApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/chapters';

    public function test_index_endpoint_returns_expected_chapter()
    {
        $this->actingAsApiEditor();
        $firstChapter = Chapter::query()->orderBy('id', 'asc')->first();

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id'       => $firstChapter->id,
                'name'     => $firstChapter->name,
                'slug'     => $firstChapter->slug,
                'book_id'  => $firstChapter->book->id,
                'priority' => $firstChapter->priority,
            ],
        ]]);
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiEditor();
        $book = $this->entities->book();
        $details = [
            'name'        => 'My API chapter',
            'description' => 'A chapter created via the API',
            'book_id'     => $book->id,
            'tags'        => [
                [
                    'name'  => 'tagname',
                    'value' => 'tagvalue',
                ],
            ],
            'priority' => 15,
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(200);
        $newItem = Chapter::query()->orderByDesc('id')->where('name', '=', $details['name'])->first();
        $resp->assertJson(array_merge($details, ['id' => $newItem->id, 'slug' => $newItem->slug]));
        $this->assertDatabaseHas('tags', [
            'entity_id'   => $newItem->id,
            'entity_type' => $newItem->getMorphClass(),
            'name'        => 'tagname',
            'value'       => 'tagvalue',
        ]);
        $resp->assertJsonMissing(['pages' => []]);
        $this->assertActivityExists('chapter_create', $newItem);
    }

    public function test_chapter_name_needed_to_create()
    {
        $this->actingAsApiEditor();
        $book = $this->entities->book();
        $details = [
            'book_id'     => $book->id,
            'description' => 'A chapter created via the API',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(422);
        $resp->assertJson($this->validationResponse([
            'name' => ['The name field is required.'],
        ]));
    }

    public function test_chapter_book_id_needed_to_create()
    {
        $this->actingAsApiEditor();
        $details = [
            'name'        => 'My api chapter',
            'description' => 'A chapter created via the API',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(422);
        $resp->assertJson($this->validationResponse([
            'book_id' => ['The book id field is required.'],
        ]));
    }

    public function test_read_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();
        $page = $chapter->pages()->first();

        $resp = $this->getJson($this->baseEndpoint . "/{$chapter->id}");
        $resp->assertStatus(200);
        $resp->assertJson([
            'id'         => $chapter->id,
            'slug'       => $chapter->slug,
            'created_by' => [
                'name' => $chapter->createdBy->name,
            ],
            'book_id'    => $chapter->book_id,
            'updated_by' => [
                'name' => $chapter->createdBy->name,
            ],
            'owned_by' => [
                'name' => $chapter->ownedBy->name,
            ],
            'pages' => [
                [
                    'id'   => $page->id,
                    'slug' => $page->slug,
                    'name' => $page->name,
                ],
            ],
        ]);
        $resp->assertJsonCount($chapter->pages()->count(), 'pages');
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();
        $details = [
            'name'        => 'My updated API chapter',
            'description' => 'A chapter created via the API',
            'tags'        => [
                [
                    'name'  => 'freshtag',
                    'value' => 'freshtagval',
                ],
            ],
            'priority'    => 15,
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$chapter->id}", $details);
        $chapter->refresh();

        $resp->assertStatus(200);
        $resp->assertJson(array_merge($details, [
            'id' => $chapter->id, 'slug' => $chapter->slug, 'book_id' => $chapter->book_id,
        ]));
        $this->assertActivityExists('chapter_update', $chapter);
    }

    public function test_update_increments_updated_date_if_only_tags_are_sent()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();
        DB::table('chapters')->where('id', '=', $chapter->id)->update(['updated_at' => Carbon::now()->subWeek()]);

        $details = [
            'tags' => [['name' => 'Category', 'value' => 'Testing']],
        ];

        $this->putJson($this->baseEndpoint . "/{$chapter->id}", $details);
        $chapter->refresh();
        $this->assertGreaterThan(Carbon::now()->subDay()->unix(), $chapter->updated_at->unix());
    }

    public function test_update_with_book_id_moves_chapter()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapterHasPages();
        $page = $chapter->pages()->first();
        $newBook = Book::query()->where('id', '!=', $chapter->book_id)->first();

        $resp = $this->putJson($this->baseEndpoint . "/{$chapter->id}", ['book_id' => $newBook->id]);
        $resp->assertOk();
        $chapter->refresh();

        $this->assertDatabaseHas('chapters', ['id' => $chapter->id, 'book_id' => $newBook->id]);
        $this->assertDatabaseHas('pages', ['id' => $page->id, 'book_id' => $newBook->id, 'chapter_id' => $chapter->id]);
    }

    public function test_update_with_new_book_id_requires_delete_permission()
    {
        $editor = $this->users->editor();
        $this->permissions->removeUserRolePermissions($editor, ['chapter-delete-all', 'chapter-delete-own']);
        $this->actingAs($editor);
        $chapter = $this->entities->chapterHasPages();
        $newBook = Book::query()->where('id', '!=', $chapter->book_id)->first();

        $resp = $this->putJson($this->baseEndpoint . "/{$chapter->id}", ['book_id' => $newBook->id]);
        $this->assertPermissionError($resp);
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();
        $resp = $this->deleteJson($this->baseEndpoint . "/{$chapter->id}");

        $resp->assertStatus(204);
        $this->assertActivityExists('chapter_delete');
    }

    public function test_export_html_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();

        $resp = $this->get($this->baseEndpoint . "/{$chapter->id}/export/html");
        $resp->assertStatus(200);
        $resp->assertSee($chapter->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.html"');
    }

    public function test_export_plain_text_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();

        $resp = $this->get($this->baseEndpoint . "/{$chapter->id}/export/plaintext");
        $resp->assertStatus(200);
        $resp->assertSee($chapter->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.txt"');
    }

    public function test_export_pdf_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();

        $resp = $this->get($this->baseEndpoint . "/{$chapter->id}/export/pdf");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.pdf"');
    }

    public function test_export_markdown_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = Chapter::visible()->has('pages')->first();

        $resp = $this->get($this->baseEndpoint . "/{$chapter->id}/export/markdown");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.md"');
        $resp->assertSee('# ' . $chapter->name);
        $resp->assertSee('# ' . $chapter->pages()->first()->name);
    }

    public function test_cant_export_when_not_have_permission()
    {
        $types = ['html', 'plaintext', 'pdf', 'markdown'];
        $this->actingAsApiEditor();
        $this->permissions->removeUserRolePermissions($this->users->editor(), ['content-export']);

        $chapter = Chapter::visible()->has('pages')->first();
        foreach ($types as $type) {
            $resp = $this->get($this->baseEndpoint . "/{$chapter->id}/export/{$type}");
            $this->assertPermissionError($resp);
        }
    }
}
