<?php

namespace Tests\User;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Activity\WatchLevels;
use Tests\TestCase;

class UserPreferencesTest extends TestCase
{
    public function test_index_view()
    {
        $resp = $this->asEditor()->get('/preferences');
        $resp->assertOk();
        $resp->assertSee('Interface Keyboard Shortcuts');
        $resp->assertSee('Edit Profile');
    }

    public function test_index_view_accessible_but_without_profile_and_notifications_for_guest_user()
    {
        $this->setSettings(['app-public' => 'true']);
        $this->permissions->grantUserRolePermissions($this->users->guest(), ['receive-notifications']);
        $resp = $this->get('/preferences');
        $resp->assertOk();
        $resp->assertSee('Interface Keyboard Shortcuts');
        $resp->assertDontSee('Edit Profile');
        $resp->assertDontSee('Notification');
    }
    public function test_interface_shortcuts_updating()
    {
        $this->asEditor();

        // View preferences with defaults
        $resp = $this->get('/preferences/shortcuts');
        $resp->assertSee('Interface Keyboard Shortcuts');

        $html = $this->withHtml($resp);
        $html->assertFieldHasValue('enabled', 'false');
        $html->assertFieldHasValue('shortcut[home_view]', '1');

        // Update preferences
        $resp = $this->put('/preferences/shortcuts', [
            'enabled' => 'true',
            'shortcut' => ['home_view' => 'Ctrl + 1'],
        ]);

        $resp->assertRedirect('/preferences/shortcuts');
        $resp->assertSessionHas('success', 'Shortcut preferences have been updated!');

        // View updates to preferences page
        $resp = $this->get('/preferences/shortcuts');
        $html = $this->withHtml($resp);
        $html->assertFieldHasValue('enabled', 'true');
        $html->assertFieldHasValue('shortcut[home_view]', 'Ctrl + 1');
    }

    public function test_body_has_shortcuts_component_when_active()
    {
        $editor = $this->users->editor();
        $this->actingAs($editor);

        $this->withHtml($this->get('/'))->assertElementNotExists('body[component="shortcuts"]');

        setting()->putUser($editor, 'ui-shortcuts-enabled', 'true');
        $this->withHtml($this->get('/'))->assertElementExists('body[component="shortcuts"]');
    }

    public function test_notification_routes_requires_notification_permission()
    {
        $viewer = $this->users->viewer();
        $resp = $this->actingAs($viewer)->get('/preferences/notifications');
        $this->assertPermissionError($resp);

        $resp = $this->put('/preferences/notifications');
        $this->assertPermissionError($resp);

        $this->permissions->grantUserRolePermissions($viewer, ['receive-notifications']);
        $resp = $this->get('/preferences/notifications');
        $resp->assertOk();
        $resp->assertSee('Notification Preferences');
    }

    public function test_notification_preferences_updating()
    {
        $editor = $this->users->editor();

        // View preferences with defaults
        $resp = $this->actingAs($editor)->get('/preferences/notifications');
        $resp->assertSee('Notification Preferences');

        $html = $this->withHtml($resp);
        $html->assertFieldHasValue('preferences[comment-replies]', 'false');

        // Update preferences
        $resp = $this->put('/preferences/notifications', [
            'preferences' => ['comment-replies' => 'true'],
        ]);

        $resp->assertRedirect('/preferences/notifications');
        $resp->assertSessionHas('success', 'Notification preferences have been updated!');

        // View updates to preferences page
        $resp = $this->get('/preferences/notifications');
        $html = $this->withHtml($resp);
        $html->assertFieldHasValue('preferences[comment-replies]', 'true');
    }

    public function test_notification_preferences_show_watches()
    {
        $editor = $this->users->editor();
        $book = $this->entities->book();

        $options = new UserEntityWatchOptions($editor, $book);
        $options->updateLevelByValue(WatchLevels::COMMENTS);

        $resp = $this->actingAs($editor)->get('/preferences/notifications');
        $resp->assertSee($book->name);
        $resp->assertSee('All Page Updates & Comments');

        $options->updateLevelByValue(WatchLevels::DEFAULT);

        $resp = $this->actingAs($editor)->get('/preferences/notifications');
        $resp->assertDontSee($book->name);
        $resp->assertDontSee('All Page Updates & Comments');
    }

    public function test_notification_preferences_not_accessible_to_guest()
    {
        $this->setSettings(['app-public' => 'true']);
        $guest = $this->users->guest();
        $this->permissions->grantUserRolePermissions($guest, ['receive-notifications']);

        $resp = $this->get('/preferences/notifications');
        $this->assertPermissionError($resp);

        $resp = $this->put('/preferences/notifications', [
            'preferences' => ['comment-replies' => 'true'],
        ]);
        $this->assertPermissionError($resp);
    }

    public function test_update_sort_preference()
    {
        $editor = $this->users->editor();
        $this->actingAs($editor);

        $updateRequest = $this->patch('/preferences/change-sort/books', [
            'sort'  => 'created_at',
            'order' => 'desc',
        ]);
        $updateRequest->assertStatus(302);

        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort',
            'value'       => 'created_at',
        ]);
        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort_order',
            'value'       => 'desc',
        ]);
        $this->assertEquals('created_at', setting()->getForCurrentUser('books_sort'));
        $this->assertEquals('desc', setting()->getForCurrentUser('books_sort_order'));
    }

    public function test_update_sort_bad_entity_type_handled()
    {
        $editor = $this->users->editor();
        $this->actingAs($editor);

        $updateRequest = $this->patch('/preferences/change-sort/dogs', [
            'sort'  => 'name',
            'order' => 'asc',
        ]);
        $updateRequest->assertStatus(500);

        $this->assertNotEmpty('name', setting()->getForCurrentUser('bookshelves_sort'));
        $this->assertNotEmpty('asc', setting()->getForCurrentUser('bookshelves_sort_order'));
    }

    public function test_update_expansion_preference()
    {
        $editor = $this->users->editor();
        $this->actingAs($editor);

        $updateRequest = $this->patch('/preferences/change-expansion/home-details', ['expand' => 'true']);
        $updateRequest->assertStatus(204);

        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':section_expansion#home-details',
            'value'       => 'true',
        ]);
        $this->assertEquals(true, setting()->getForCurrentUser('section_expansion#home-details'));

        $invalidKeyRequest = $this->patch('/preferences/change-expansion/my-home-details', ['expand' => 'true']);
        $invalidKeyRequest->assertStatus(500);
    }

    public function test_toggle_dark_mode()
    {
        $home = $this->actingAs($this->users->editor())->get('/');
        $home->assertSee('Dark Mode');
        $this->withHtml($home)->assertElementNotExists('.dark-mode');

        $this->assertEquals(false, setting()->getForCurrentUser('dark-mode-enabled', false));
        $prefChange = $this->patch('/preferences/toggle-dark-mode');
        $prefChange->assertRedirect();
        $this->assertEquals(true, setting()->getForCurrentUser('dark-mode-enabled'));

        $home = $this->actingAs($this->users->editor())->get('/');
        $this->withHtml($home)->assertElementExists('.dark-mode');
        $home->assertDontSee('Dark Mode');
        $home->assertSee('Light Mode');
    }

    public function test_dark_mode_defaults_to_config_option()
    {
        config()->set('setting-defaults.user.dark-mode-enabled', false);
        $this->assertEquals(false, setting()->getForCurrentUser('dark-mode-enabled'));
        $home = $this->get('/login');
        $this->withHtml($home)->assertElementNotExists('.dark-mode');

        config()->set('setting-defaults.user.dark-mode-enabled', true);
        $this->assertEquals(true, setting()->getForCurrentUser('dark-mode-enabled'));
        $home = $this->get('/login');
        $this->withHtml($home)->assertElementExists('.dark-mode');
    }

    public function test_books_view_type_preferences_when_list()
    {
        $editor = $this->users->editor();
        setting()->putUser($editor, 'books_view_type', 'list');

        $resp = $this->actingAs($editor)->get('/books');
        $this->withHtml($resp)
            ->assertElementNotExists('.featured-image-container')
            ->assertElementExists('.content-wrap .entity-list-item');
    }

    public function test_books_view_type_preferences_when_grid()
    {
        $editor = $this->users->editor();
        setting()->putUser($editor, 'books_view_type', 'grid');

        $resp = $this->actingAs($editor)->get('/books');
        $this->withHtml($resp)->assertElementExists('.featured-image-container');
    }

    public function test_shelf_view_type_change()
    {
        $editor = $this->users->editor();
        $shelf = $this->entities->shelf();
        setting()->putUser($editor, 'bookshelf_view_type', 'list');

        $resp = $this->actingAs($editor)->get($shelf->getUrl())->assertSee('Grid View');
        $this->withHtml($resp)
            ->assertElementNotExists('.featured-image-container')
            ->assertElementExists('.content-wrap .entity-list-item');

        $req = $this->patch("/preferences/change-view/bookshelf", ['view' => 'grid']);
        $req->assertRedirect($shelf->getUrl());

        $resp = $this->actingAs($editor)->get($shelf->getUrl())
            ->assertSee('List View');

        $this->withHtml($resp)
            ->assertElementExists('.featured-image-container')
            ->assertElementNotExists('.content-wrap .entity-list-item');
    }

    public function test_update_code_language_favourite()
    {
        $editor = $this->users->editor();
        $page = $this->entities->page();
        $this->actingAs($editor);

        $this->patch('/preferences/update-code-language-favourite', ['language' => 'php', 'active' => true]);
        $this->patch('/preferences/update-code-language-favourite', ['language' => 'javascript', 'active' => true]);

        $resp = $this->get($page->getUrl('/edit'));
        $resp->assertSee('option:code-editor:favourites="php,javascript"', false);

        $this->patch('/preferences/update-code-language-favourite', ['language' => 'ruby', 'active' => true]);
        $this->patch('/preferences/update-code-language-favourite', ['language' => 'php', 'active' => false]);

        $resp = $this->get($page->getUrl('/edit'));
        $resp->assertSee('option:code-editor:favourites="javascript,ruby"', false);
    }
}
