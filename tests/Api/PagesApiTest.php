<?php

namespace Tests\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class PagesApiTest extends TestCase
{
    use TestsApi;

    protected $baseEndpoint = '/api/pages';

    public function test_index_endpoint_returns_expected_page()
    {
        $this->actingAsApiEditor();
        $firstPage = Page::query()->orderBy('id', 'asc')->first();

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id'       => $firstPage->id,
                'name'     => $firstPage->name,
                'slug'     => $firstPage->slug,
                'book_id'  => $firstPage->book->id,
                'priority' => $firstPage->priority,
            ],
        ]]);
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::query()->first();
        $details = [
            'name'    => 'My API page',
            'book_id' => $book->id,
            'html'    => '<p>My new page content</p>',
            'tags'    => [
                [
                    'name'  => 'tagname',
                    'value' => 'tagvalue',
                ],
            ],
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        unset($details['html']);
        $resp->assertStatus(200);
        $newItem = Page::query()->orderByDesc('id')->where('name', '=', $details['name'])->first();
        $resp->assertJson(array_merge($details, ['id' => $newItem->id, 'slug' => $newItem->slug]));
        $this->assertDatabaseHas('tags', [
            'entity_id'   => $newItem->id,
            'entity_type' => $newItem->getMorphClass(),
            'name'        => 'tagname',
            'value'       => 'tagvalue',
        ]);
        $resp->assertSeeText('My new page content');
        $resp->assertJsonMissing(['book' => []]);
        $this->assertActivityExists('page_create', $newItem);
    }

    public function test_page_name_needed_to_create()
    {
        $this->actingAsApiEditor();
        $book = Book::query()->first();
        $details = [
            'book_id' => $book->id,
            'html'    => '<p>A page created via the API</p>',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(422);
        $resp->assertJson($this->validationResponse([
            'name' => ['The name field is required.'],
        ]));
    }

    public function test_book_id_or_chapter_id_needed_to_create()
    {
        $this->actingAsApiEditor();
        $details = [
            'name' => 'My api page',
            'html' => '<p>A page created via the API</p>',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(422);
        $resp->assertJson($this->validationResponse([
            'book_id'    => ['The book id field is required when chapter id is not present.'],
            'chapter_id' => ['The chapter id field is required when book id is not present.'],
        ]));

        $chapter = Chapter::visible()->first();
        $resp = $this->postJson($this->baseEndpoint, array_merge($details, ['chapter_id' => $chapter->id]));
        $resp->assertStatus(200);

        $book = Book::visible()->first();
        $resp = $this->postJson($this->baseEndpoint, array_merge($details, ['book_id' => $book->id]));
        $resp->assertStatus(200);
    }

    public function test_markdown_can_be_provided_for_create()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->first();
        $details = [
            'book_id'  => $book->id,
            'name'     => 'My api page',
            'markdown' => "# A new API page \n[link](https://example.com)",
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertJson(['markdown' => $details['markdown']]);

        $respHtml = $resp->json('html');
        $this->assertStringContainsString('new API page</h1>', $respHtml);
        $this->assertStringContainsString('link</a>', $respHtml);
        $this->assertStringContainsString('href="https://example.com"', $respHtml);
    }

    public function test_read_endpoint()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();

        $resp = $this->getJson($this->baseEndpoint . "/{$page->id}");
        $resp->assertStatus(200);
        $resp->assertJson([
            'id'         => $page->id,
            'slug'       => $page->slug,
            'created_by' => [
                'name' => $page->createdBy->name,
            ],
            'book_id'    => $page->book_id,
            'updated_by' => [
                'name' => $page->createdBy->name,
            ],
            'owned_by' => [
                'name' => $page->ownedBy->name,
            ],
        ]);
    }

    public function test_read_endpoint_provides_rendered_html()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();
        $page->html = "<p>testing</p><script>alert('danger')</script><h1>Hello</h1>";
        $page->save();

        $resp = $this->getJson($this->baseEndpoint . "/{$page->id}");
        $html = $resp->json('html');
        $this->assertStringNotContainsString('script', $html);
        $this->assertStringContainsString('Hello', $html);
        $this->assertStringContainsString('testing', $html);
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();
        $details = [
            'name' => 'My updated API page',
            'html' => '<p>A page created via the API</p>',
            'tags' => [
                [
                    'name'  => 'freshtag',
                    'value' => 'freshtagval',
                ],
            ],
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$page->id}", $details);
        $page->refresh();

        $resp->assertStatus(200);
        unset($details['html']);
        $resp->assertJson(array_merge($details, [
            'id' => $page->id, 'slug' => $page->slug, 'book_id' => $page->book_id,
        ]));
        $this->assertActivityExists('page_update', $page);
    }

    public function test_providing_new_chapter_id_on_update_will_move_page()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();
        $chapter = Chapter::visible()->where('book_id', '!=', $page->book_id)->first();
        $details = [
            'name'       => 'My updated API page',
            'chapter_id' => $chapter->id,
            'html'       => '<p>A page created via the API</p>',
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$page->id}", $details);
        $resp->assertStatus(200);
        $resp->assertJson([
            'chapter_id' => $chapter->id,
            'book_id'    => $chapter->book_id,
        ]);
    }

    public function test_providing_move_via_update_requires_page_create_permission_on_new_parent()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();
        $chapter = Chapter::visible()->where('book_id', '!=', $page->book_id)->first();
        $this->setEntityRestrictions($chapter, ['view'], [$this->getEditor()->roles()->first()]);
        $details = [
            'name'       => 'My updated API page',
            'chapter_id' => $chapter->id,
            'html'       => '<p>A page created via the API</p>',
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$page->id}", $details);
        $resp->assertStatus(403);
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();
        $resp = $this->deleteJson($this->baseEndpoint . "/{$page->id}");

        $resp->assertStatus(204);
        $this->assertActivityExists('page_delete', $page);
    }

    public function test_export_html_endpoint()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$page->id}/export/html");
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.html"');
    }

    public function test_export_plain_text_endpoint()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$page->id}/export/plaintext");
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.txt"');
    }

    public function test_export_pdf_endpoint()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$page->id}/export/pdf");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.pdf"');
    }

    public function test_export_markdown_endpoint()
    {
        $this->actingAsApiEditor();
        $page = Page::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$page->id}/export/markdown");
        $resp->assertStatus(200);
        $resp->assertSee('# ' . $page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.md"');
    }
}
