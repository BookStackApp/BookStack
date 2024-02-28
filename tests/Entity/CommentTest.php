<?php

namespace Tests\Entity;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Comment;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function test_add_comment()
    {
        $this->asAdmin();
        $page = $this->entities->page();

        $comment = Comment::factory()->make(['parent_id' => 2]);
        $resp = $this->postJson("/comment/$page->id", $comment->getAttributes());

        $resp->assertStatus(200);
        $resp->assertSee($comment->html, false);

        $pageResp = $this->get($page->getUrl());
        $pageResp->assertSee($comment->html, false);

        $this->assertDatabaseHas('comments', [
            'local_id'    => 1,
            'entity_id'   => $page->id,
            'entity_type' => Page::newModelInstance()->getMorphClass(),
            'text'        => null,
            'parent_id'   => 2,
        ]);

        $this->assertActivityExists(ActivityType::COMMENT_CREATE);
    }

    public function test_comment_edit()
    {
        $this->asAdmin();
        $page = $this->entities->page();

        $comment = Comment::factory()->make();
        $this->postJson("/comment/$page->id", $comment->getAttributes());

        $comment = $page->comments()->first();
        $newHtml = '<p>updated text content</p>';
        $resp = $this->putJson("/comment/$comment->id", [
            'html' => $newHtml,
        ]);

        $resp->assertStatus(200);
        $resp->assertSee($newHtml, false);
        $resp->assertDontSee($comment->html, false);

        $this->assertDatabaseHas('comments', [
            'html'      => $newHtml,
            'entity_id' => $page->id,
        ]);

        $this->assertActivityExists(ActivityType::COMMENT_UPDATE);
    }

    public function test_comment_delete()
    {
        $this->asAdmin();
        $page = $this->entities->page();

        $comment = Comment::factory()->make();
        $this->postJson("/comment/$page->id", $comment->getAttributes());

        $comment = $page->comments()->first();

        $resp = $this->delete("/comment/$comment->id");
        $resp->assertStatus(200);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);

        $this->assertActivityExists(ActivityType::COMMENT_DELETE);
    }

    public function test_scripts_cannot_be_injected_via_comment_html()
    {
        $page = $this->entities->page();

        $script = '<script>const a = "script";</script><p onclick="1">My lovely comment</p>';
        $this->asAdmin()->postJson("/comment/$page->id", [
            'html' => $script,
        ]);

        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($script, false);
        $pageView->assertSee('<p>My lovely comment</p>', false);

        $comment = $page->comments()->first();
        $this->putJson("/comment/$comment->id", [
            'html' => $script . '<p>updated</p>',
        ]);

        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($script, false);
        $pageView->assertSee('<p>My lovely comment</p><p>updated</p>');
    }

    public function test_scripts_are_removed_even_if_already_in_db()
    {
        $page = $this->entities->page();
        Comment::factory()->create([
            'html' => '<script>superbadscript</script><p onclick="superbadonclick">scriptincommentest</p>',
            'entity_type' => 'page', 'entity_id' => $page
        ]);

        $resp = $this->asAdmin()->get($page->getUrl());
        $resp->assertSee('scriptincommentest', false);
        $resp->assertDontSee('superbadscript', false);
        $resp->assertDontSee('superbadonclick', false);
    }

    public function test_comment_html_is_limited()
    {
        $page = $this->entities->page();
        $input = '<h1>Test</h1><p id="abc" href="beans">Content<a href="#cat" data-a="b">a</a><section>Hello</section></p>';
        $expected = '<p>Content<a href="#cat">a</a></p>';

        $resp = $this->asAdmin()->post("/comment/{$page->id}", ['html' => $input]);
        $resp->assertOk();
        $this->assertDatabaseHas('comments', [
           'entity_type' => 'page',
           'entity_id' => $page->id,
           'html' => $expected,
        ]);

        $comment = $page->comments()->first();
        $resp = $this->put("/comment/{$comment->id}", ['html' => $input]);
        $resp->assertOk();
        $this->assertDatabaseHas('comments', [
            'id'   => $comment->id,
            'html' => $expected,
        ]);
    }

    public function test_reply_comments_are_nested()
    {
        $this->asAdmin();
        $page = $this->entities->page();

        $this->postJson("/comment/$page->id", ['html' => '<p>My new comment</p>']);
        $this->postJson("/comment/$page->id", ['html' => '<p>My new comment</p>']);

        $respHtml = $this->withHtml($this->get($page->getUrl()));
        $respHtml->assertElementCount('.comment-branch', 3);
        $respHtml->assertElementNotExists('.comment-branch .comment-branch');

        $comment = $page->comments()->first();
        $resp = $this->postJson("/comment/$page->id", [
            'html' => '<p>My nested comment</p>', 'parent_id' => $comment->local_id
        ]);
        $resp->assertStatus(200);

        $respHtml = $this->withHtml($this->get($page->getUrl()));
        $respHtml->assertElementCount('.comment-branch', 4);
        $respHtml->assertElementContains('.comment-branch .comment-branch', 'My nested comment');
    }

    public function test_comments_are_visible_in_the_page_editor()
    {
        $page = $this->entities->page();

        $this->asAdmin()->postJson("/comment/$page->id", ['html' => '<p>My great comment to see in the editor</p>']);

        $respHtml = $this->withHtml($this->get($page->getUrl('/edit')));
        $respHtml->assertElementContains('.comment-box .content', 'My great comment to see in the editor');
    }

    public function test_comment_creator_name_truncated()
    {
        [$longNamedUser] = $this->users->newUserWithRole(['name' => 'Wolfeschlegelsteinhausenbergerdorff'], ['comment-create-all', 'page-view-all']);
        $page = $this->entities->page();

        $comment = Comment::factory()->make();
        $this->actingAs($longNamedUser)->postJson("/comment/$page->id", $comment->getAttributes());

        $pageResp = $this->asAdmin()->get($page->getUrl());
        $pageResp->assertSee('Wolfeschlegels…');
    }

    public function test_comment_editor_js_loaded_with_create_or_edit_permissions()
    {
        $editor = $this->users->editor();
        $page = $this->entities->page();

        $resp = $this->actingAs($editor)->get($page->getUrl());
        $resp->assertSee('tinymce.min.js?', false);
        $resp->assertSee('window.editor_translations', false);
        $resp->assertSee('component="entity-selector"', false);

        $this->permissions->removeUserRolePermissions($editor, ['comment-create-all']);
        $this->permissions->grantUserRolePermissions($editor, ['comment-update-own']);

        $resp = $this->actingAs($editor)->get($page->getUrl());
        $resp->assertDontSee('tinymce.min.js?', false);
        $resp->assertDontSee('window.editor_translations', false);
        $resp->assertDontSee('component="entity-selector"', false);

        Comment::factory()->create([
            'created_by'  => $editor->id,
            'entity_type' => 'page',
            'entity_id'   => $page->id,
        ]);

        $resp = $this->actingAs($editor)->get($page->getUrl());
        $resp->assertSee('tinymce.min.js?', false);
        $resp->assertSee('window.editor_translations', false);
        $resp->assertSee('component="entity-selector"', false);
    }
}
