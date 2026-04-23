<?php

namespace Api;

use BookStack\Activity\Models\Tag;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Tests\Api\TestsApi;
use Tests\TestCase;

class TagsApiTest extends TestCase
{
    use TestsApi;

    public function test_list_names_provides_rolled_up_tag_info(): void
    {
        $tagInfo = ['name' => 'MyGreatApiTag', 'value' => 'cat'];
        $pagesToTag = Page::query()->take(10)->get();
        $booksToTag = Book::query()->take(3)->get();
        $chaptersToTag = Chapter::query()->take(5)->get();
        $pagesToTag->each(fn (Page $page) => $page->tags()->save(new Tag($tagInfo)));
        $booksToTag->each(fn (Book $book) => $book->tags()->save(new Tag($tagInfo)));
        $chaptersToTag->each(fn (Chapter $chapter) => $chapter->tags()->save(new Tag($tagInfo)));

        $resp = $this->actingAsApiEditor()->getJson('api/tags/names?filter[name]=MyGreatApiTag');
        $resp->assertStatus(200);
        $resp->assertJson([
            'data' => [
                [
                    'name' => 'MyGreatApiTag',
                    'values' => 1,
                    'usages' => 18,
                    'page_count' => 10,
                    'book_count' => 3,
                    'chapter_count' => 5,
                    'shelf_count' => 0,
                ]
            ],
            'total' => 1,
        ]);
    }

    public function test_list_names_is_limited_by_permission_visibility(): void
    {
        $pagesToTag = Page::query()->take(10)->get();
        $pagesToTag->each(fn (Page $page) => $page->tags()->save(new Tag(['name' => 'MyGreatApiTag', 'value' => 'cat' . $page->id])));

        $this->permissions->disableEntityInheritedPermissions($pagesToTag[3]);
        $this->permissions->disableEntityInheritedPermissions($pagesToTag[6]);

        $resp = $this->actingAsApiEditor()->getJson('api/tags/names?filter[name]=MyGreatApiTag');
        $resp->assertStatus(200);
        $resp->assertJson([
            'data' => [
                [
                    'name' => 'MyGreatApiTag',
                    'values' => 8,
                    'usages' => 8,
                    'page_count' => 8,
                    'book_count' => 0,
                    'chapter_count' => 0,
                    'shelf_count' => 0,
                ]
            ],
            'total' => 1,
        ]);
    }

    public function test_list_values_returns_values_for_set_tag()
    {
        $pagesToTag = Page::query()->take(10)->get();
        $booksToTag = Book::query()->take(3)->get();
        $chaptersToTag = Chapter::query()->take(5)->get();
        $pagesToTag->each(fn (Page $page) => $page->tags()->save(new Tag(['name' => 'MyValueApiTag', 'value' => 'tag-page' . $page->id])));
        $booksToTag->each(fn (Book $book) => $book->tags()->save(new Tag(['name' => 'MyValueApiTag', 'value' => 'tag-book' . $book->id])));
        $chaptersToTag->each(fn (Chapter $chapter) => $chapter->tags()->save(new Tag(['name' => 'MyValueApiTag', 'value' => 'tag-chapter' . $chapter->id])));

        $resp = $this->actingAsApiEditor()->getJson('api/tags/values-for-name?name=MyValueApiTag');

        $resp->assertStatus(200);
        $resp->assertJson(['total' => 18]);
        $resp->assertJsonFragment([
            [
                'name' => 'MyValueApiTag',
                'value' => 'tag-page' . $pagesToTag[0]->id,
                'usages' => 1,
                'page_count' => 1,
                'book_count' => 0,
                'chapter_count' => 0,
                'shelf_count' => 0,
            ]
        ]);
    }

    public function test_list_values_is_limited_by_permission_visibility(): void
    {
        $pagesToTag = Page::query()->take(10)->get();
        $pagesToTag->each(fn (Page $page) => $page->tags()->save(new Tag(['name' => 'MyGreatApiTag', 'value' => 'cat' . $page->id])));

        $this->permissions->disableEntityInheritedPermissions($pagesToTag[3]);
        $this->permissions->disableEntityInheritedPermissions($pagesToTag[6]);

        $resp = $this->actingAsApiEditor()->getJson('api/tags/values-for-name?name=MyGreatApiTag');
        $resp->assertStatus(200);
        $resp->assertJson(['total' => 8]);
        $resp->assertJsonMissing(['value' => 'cat' . $pagesToTag[3]->id]);
    }
}
