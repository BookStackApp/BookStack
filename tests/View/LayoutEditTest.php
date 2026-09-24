<?php

namespace Tests\View;

use BookStack\Activity\Models\Favourite;
use Tests\TestCase;

class LayoutEditTest extends TestCase
{
    public function test_route_access_limited_to_logged_in_users()
    {
        $this->setSettings(['app-public' => 'true']);

        $this->get('/layouts/books-show')->assertRedirect('/');
        $this->put('/layouts/books-show')->assertRedirect('/');
        $this->put('/layouts/books-show/reset')->assertRedirect('/');
    }

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

    public function test_view_only_shows_center_column_for_relevant_layouts()
    {
        $resp = $this->asEditor()->get('/layouts/books-show');
        $html = $this->withHtml($resp);
        $html->assertElementCount('[data-column="center"]', 0);

        $resp = $this->asEditor()->get('/layouts/home-default');
        $html = $this->withHtml($resp);
        $html->assertElementCount('[data-column="center"]', 1);
    }

    public function test_only_single_homepage_option_shows_depending_on_app_homepage_setting()
    {
        $resp = $this->asEditor()->get('/layouts/books-show');
        $html = $this->withHtml($resp);
        $html->assertLinkExists(url('/layouts/home-default'), 'Homepage');
        $html->assertLinkNotExists(url('/layouts/home-non-default'), 'Homepage');

        $this->setSettings(['app-homepage-type' => 'books']);

        $resp = $this->asEditor()->get('/layouts/books-show');
        $html = $this->withHtml($resp);
        $html->assertLinkExists(url('/layouts/home-non-default'), 'Homepage');
        $html->assertLinkNotExists(url('/layouts/home-default'), 'Homepage');
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

    public function test_malformed_update()
    {
        $layout = [
            'left' => 'a',
            'right' => ['builtin_books-show-activity', 5],
        ];

        $resp = $this->asEditor()->put('/layouts/books-show', ['layout' => json_encode($layout)]);
        $resp->assertRedirect('/layouts/books-show');

        $userPreferenceString = setting()->getForCurrentUser('view-layout#books-show');
        $this->assertJson($userPreferenceString);
        $storedData = json_decode($userPreferenceString, true);

        $this->assertEquals([
            'right' => [
                'builtin_books-show-activity',
            ],
        ], $storedData);
    }

    public function test_layout_changes_take_effect()
    {
        $editor = $this->users->editor();
        $this->actingAs($editor);
        $page = $this->entities->page();
        $page->favourites()->save((new Favourite())->forceFill(['user_id' => $editor->id]));
        $this->get($page->getUrl());

        $html = $this->withHtml($this->get('/'));

        $html->assertElementExists('.grid.third > div:nth-child(1) h3:contains("My Recently Viewed")');
        $html->assertElementExists('.grid.third > div:nth-child(2) h3:contains("My Most Viewed Favourites")');
        $html->assertElementExists('.grid.third > div:nth-child(2) h3:contains("Recently Updated Pages")');
        $html->assertElementExists('.grid.third > div:nth-child(3) h3:contains("Recent Activity")');

        $layout = [
            'left' => [
                'builtin_home-top-favourites',
            ],
            'center' => [
                'builtin_home-recent-activity',
            ],
            'right' => [
                'builtin_home-recently-viewed-or-recent-books',
            ],
            'unused' => [
                'builtin_home-recently-updated-pages',
            ],
        ];

        $this->put('/layouts/home-default', ['layout' => json_encode($layout)])->assertRedirect('/layouts/home-default');
        $html = $this->withHtml($this->get('/'));

        $html->assertElementExists('.grid.third > div:nth-child(3) h3:contains("My Recently Viewed")');
        $html->assertElementExists('.grid.third > div:nth-child(1) h3:contains("My Most Viewed Favourites")');
        $html->assertElementNotExists('h3:contains("Recently Updated Pages")');
        $html->assertElementExists('.grid.third > div:nth-child(2) h3:contains("Recent Activity")');
    }

    public function test_reset()
    {
        $this->asEditor();

        $settingKey = 'view-layout#books-show';
        setting()->putForCurrentUser($settingKey, json_encode(['left' => [], 'right' => [], 'unused' => ['builtin_books-show-activity']]));

        $resp = $this->asEditor()->put('/layouts/books-show/reset');
        $resp->assertRedirect('/layouts/books-show');
        $this->assertSessionHas('success');

        $this->assertFalse(setting()->getForCurrentUser($settingKey));
    }
}
