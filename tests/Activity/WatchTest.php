<?php

namespace Tests\Activity;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Activity\WatchLevels;
use BookStack\Entities\Models\Entity;
use Tests\TestCase;

class WatchTest extends TestCase
{
    public function test_watch_action_exists_on_entity_unless_active()
    {
        $editor = $this->users->editor();
        $this->actingAs($editor);

        $entities = [$this->entities->book(), $this->entities->chapter(), $this->entities->page()];
        /** @var Entity $entity */
        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $this->withHtml($resp)->assertElementContains('form[action$="/watching/update"] button.icon-list-item', 'Watch');

            $watchOptions = new UserEntityWatchOptions($editor, $entity);
            $watchOptions->updateWatchLevel('comments');

            $resp = $this->get($entity->getUrl());
            $this->withHtml($resp)->assertElementNotExists('form[action$="/watching/update"] button.icon-list-item');
        }
    }

    public function test_watch_action_only_shows_with_permission()
    {
        $viewer = $this->users->viewer();
        $this->actingAs($viewer);

        $entities = [$this->entities->book(), $this->entities->chapter(), $this->entities->page()];
        /** @var Entity $entity */
        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $this->withHtml($resp)->assertElementNotExists('form[action$="/watching/update"] button.icon-list-item');
        }

        $this->permissions->grantUserRolePermissions($viewer, ['receive-notifications']);

        /** @var Entity $entity */
        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $this->withHtml($resp)->assertElementExists('form[action$="/watching/update"] button.icon-list-item');
        }
    }

    public function test_watch_update()
    {
        $editor = $this->users->editor();
        $book = $this->entities->book();

        $this->actingAs($editor)->get($book->getUrl());
        $resp = $this->put('/watching/update', [
            'type' => get_class($book),
            'id' => $book->id,
            'level' => 'comments'
        ]);

        $resp->assertRedirect($book->getUrl());
        $this->assertSessionHas('success');
        $this->assertDatabaseHas('watches', [
            'watchable_id' => $book->id,
            'watchable_type' => $book->getMorphClass(),
            'user_id' => $editor->id,
            'level' => WatchLevels::COMMENTS,
        ]);

        $resp = $this->put('/watching/update', [
            'type' => get_class($book),
            'id' => $book->id,
            'level' => 'default'
        ]);
        $resp->assertRedirect($book->getUrl());
        $this->assertDatabaseMissing('watches', [
            'watchable_id' => $book->id,
            'watchable_type' => $book->getMorphClass(),
            'user_id' => $editor->id,
        ]);
    }

    public function test_watch_detail_display_reflects_state()
    {
        $editor = $this->users->editor();
        $book = $this->entities->bookHasChaptersAndPages();
        $chapter = $book->chapters()->first();
        $page = $chapter->pages()->first();

        (new UserEntityWatchOptions($editor, $book))->updateWatchLevel('updates');

        $this->actingAs($editor)->get($book->getUrl())->assertSee('Watching new pages and updates');
        $this->get($chapter->getUrl())->assertSee('Watching via parent book');
        $this->get($page->getUrl())->assertSee('Watching via parent book');

        (new UserEntityWatchOptions($editor, $chapter))->updateWatchLevel('comments');
        $this->get($chapter->getUrl())->assertSee('Watching new pages, updates & comments');
        $this->get($page->getUrl())->assertSee('Watching via parent chapter');

        (new UserEntityWatchOptions($editor, $page))->updateWatchLevel('updates');
        $this->get($page->getUrl())->assertSee('Watching new pages and updates');
    }

    public function test_watch_detail_ignore_indicator_cascades()
    {
        $editor = $this->users->editor();
        $book = $this->entities->bookHasChaptersAndPages();
        (new UserEntityWatchOptions($editor, $book))->updateWatchLevel('ignore');

        $this->actingAs($editor)->get($book->getUrl())->assertSee('Ignoring notifications');
        $this->get($book->chapters()->first()->getUrl())->assertSee('Ignoring via parent book');
        $this->get($book->pages()->first()->getUrl())->assertSee('Ignoring via parent book');
    }

    public function test_watch_option_menu_shows_current_active_state()
    {
        $editor = $this->users->editor();
        $book = $this->entities->book();
        $options = new UserEntityWatchOptions($editor, $book);

        $respHtml = $this->withHtml($this->actingAs($editor)->get($book->getUrl()));
        $respHtml->assertElementNotExists('form[action$="/watching/update"] svg[data-icon="check-circle"]');

        $options->updateWatchLevel('comments');
        $respHtml = $this->withHtml($this->actingAs($editor)->get($book->getUrl()));
        $respHtml->assertElementExists('form[action$="/watching/update"] button[value="comments"] svg[data-icon="check-circle"]');

        $options->updateWatchLevel('ignore');
        $respHtml = $this->withHtml($this->actingAs($editor)->get($book->getUrl()));
        $respHtml->assertElementExists('form[action$="/watching/update"] button[value="ignore"] svg[data-icon="check-circle"]');
    }

    public function test_watch_option_menu_limits_options_for_pages()
    {
        $editor = $this->users->editor();
        $book = $this->entities->bookHasChaptersAndPages();
        (new UserEntityWatchOptions($editor, $book))->updateWatchLevel('ignore');

        $respHtml = $this->withHtml($this->actingAs($editor)->get($book->getUrl()));
        $respHtml->assertElementExists('form[action$="/watching/update"] button[name="level"][value="new"]');

        $respHtml = $this->withHtml($this->get($book->pages()->first()->getUrl()));
        $respHtml->assertElementExists('form[action$="/watching/update"] button[name="level"][value="updates"]');
        $respHtml->assertElementNotExists('form[action$="/watching/update"] button[name="level"][value="new"]');
    }

    // TODO - Guest user cannot see/set notifications
    // TODO - Actual notification testing
}
