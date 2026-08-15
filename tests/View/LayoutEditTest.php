<?php

namespace Tests\View;

use Tests\TestCase;

class LayoutEditTest extends TestCase
{
    public function test_view()
    {
        $resp = $this->asEditor()->get('/layouts/books-index');
        $resp->assertOk();

        $resp->assertSee('Back to Preferences');
        $resp->assertSee('Edit Layout');
        $resp->assertSee('Popular Books');
        $resp->assertDontSee('My Recent Drafts');
    }

    public function test_view_loads_existing_preferences()
    {
        $this->asEditor();
        setting()->putForCurrentUser('view-layout#books-show', json_encode([
            'left' => [
            ],
            'right' => [
            ],
            'unused' => [
                'builtin_books-show-search-form',
                'builtin_books-show-actions',
                'builtin_books-show-activity',
                'builtin_books-show-shelves',
                'builtin_books-show-tags',
                'builtin_books-show-details',
            ],
        ]));

        $resp = $this->get('/layouts/books-show');
        $resp->assertOk();
        $html = $this->withHtml($resp);

        $html->assertElementCount('[data-column="unused"] > li', 7);
        $html->assertElementCount('[data-column="left"] > li', 1);
        $html->assertElementCount('[data-column="right"] > li', 1);
    }

    public function test_update()
    {
        $layout = [
            'left' => [
                'builtin_books-show-search-form',
                'builtin_books-show-actions',
            ],
            'center' => [
                'builtin_books-show-tags', // Should be ignored as there's no center position
            ],
            'right' => [
                'builtin_books-show-activity',
                'totally_invalid_value', // Should be removed
            ],
            'unused' => [
                'builtin_books-show-shelves',
            ],
        ];

        $resp = $this->asEditor()->put('/layouts/books-show', ['layout' => json_encode($layout)]);
        $resp->assertRedirect('/layouts/books-show');
        $this->assertSessionHas('success');

        $userPreferenceString = setting()->getForCurrentUser('view-layout#books-show');
        $this->assertJson($userPreferenceString);
        $storedData = json_decode($userPreferenceString, true);

        $this->assertEquals([
            'left' => [
                'builtin_books-show-search-form',
                'builtin_books-show-actions',
            ],
            'right' => [
                'builtin_books-show-activity',
            ],
            'unused' => [
                'builtin_books-show-shelves',
            ],
        ], $storedData);
    }
}
