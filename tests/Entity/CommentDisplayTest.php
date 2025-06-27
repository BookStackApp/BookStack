<?php

namespace Tests\Entity;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Comment;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class CommentDisplayTest extends TestCase
{
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
        $resp->assertSee('window.editor_translations', false);
        $resp->assertSee('component="entity-selector"', false);

        $this->permissions->removeUserRolePermissions($editor, ['comment-create-all']);
        $this->permissions->grantUserRolePermissions($editor, ['comment-update-own']);

        $resp = $this->actingAs($editor)->get($page->getUrl());
        $resp->assertDontSee('window.editor_translations', false);
        $resp->assertDontSee('component="entity-selector"', false);

        Comment::factory()->create([
            'created_by'  => $editor->id,
            'entity_type' => 'page',
            'entity_id'   => $page->id,
        ]);

        $resp = $this->actingAs($editor)->get($page->getUrl());
        $resp->assertSee('window.editor_translations', false);
        $resp->assertSee('component="entity-selector"', false);
    }

    public function test_comment_displays_relative_times()
    {
        $page = $this->entities->page();
        $comment = Comment::factory()->create(['entity_id' => $page->id, 'entity_type' => $page->getMorphClass()]);
        $comment->created_at = now()->subWeek();
        $comment->updated_at = now()->subDay();
        $comment->save();

        $pageResp = $this->asAdmin()->get($page->getUrl());
        $html = $this->withHtml($pageResp);

        // Create date shows relative time as text to user
        $html->assertElementContains('.comment-box', 'commented 1 week ago');
        // Updated indicator has full time as title
        $html->assertElementContains('.comment-box span[title^="Updated ' . $comment->updated_at->format('Y-m-d') .  '"]', 'Updated');
    }

    public function test_comment_displays_reference_if_set()
    {
        $page = $this->entities->page();
        $comment = Comment::factory()->make([
            'content_ref' => 'bkmrk-a:abc:4-1',
            'local_id'   =>  10,
        ]);
        $page->comments()->save($comment);

        $html = $this->withHtml($this->asEditor()->get($page->getUrl()));
        $html->assertElementExists('#comment10 .comment-reference-indicator-wrap a');
    }

    public function test_archived_comments_are_shown_in_their_own_container()
    {
        $page = $this->entities->page();
        $comment = Comment::factory()->make(['local_id' => 44]);
        $page->comments()->save($comment);

        $html = $this->withHtml($this->asEditor()->get($page->getUrl()));
        $html->assertElementExists('#comment-tab-panel-active #comment44');
        $html->assertElementNotExists('#comment-tab-panel-archived .comment-box');

        $comment->archived = true;
        $comment->save();

        $html = $this->withHtml($this->asEditor()->get($page->getUrl()));
        $html->assertElementExists('#comment-tab-panel-archived #comment44.comment-box');
        $html->assertElementNotExists('#comment-tab-panel-active #comment44');
    }
}
