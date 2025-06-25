<?php

namespace Tests\Entity;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Comment;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class CommentStoreTest extends TestCase
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
    public function test_add_comment_stores_content_reference_only_if_format_valid()
    {
        $validityByRefs = [
            'bkmrk-my-title:4589284922:4-3' => true,
            'bkmrk-my-title:4589284922:' => true,
            'bkmrk-my-title:4589284922:abc' => false,
            'my-title:4589284922:' => false,
            'bkmrk-my-title-4589284922:' => false,
        ];

        $page = $this->entities->page();

        foreach ($validityByRefs as $ref => $valid) {
            $this->asAdmin()->postJson("/comment/$page->id", [
                'html' => '<p>My comment</p>',
                'parent_id' => null,
                'content_ref' => $ref,
            ]);

            if ($valid) {
                $this->assertDatabaseHas('comments', ['entity_id' => $page->id, 'content_ref' => $ref]);
            } else {
                $this->assertDatabaseMissing('comments', ['entity_id' => $page->id, 'content_ref' => $ref]);
            }
        }
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

    public function test_comment_archive_and_unarchive()
    {
        $this->asAdmin();
        $page = $this->entities->page();

        $comment = Comment::factory()->make();
        $page->comments()->save($comment);
        $comment->refresh();

        $this->put("/comment/$comment->id/archive");

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'archived' => true,
        ]);

        $this->assertActivityExists(ActivityType::COMMENT_UPDATE);

        $this->put("/comment/$comment->id/unarchive");

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'archived' => false,
        ]);

        $this->assertActivityExists(ActivityType::COMMENT_UPDATE);
    }

    public function test_archive_endpoints_require_delete_or_edit_permissions()
    {
        $viewer = $this->users->viewer();
        $page = $this->entities->page();

        $comment = Comment::factory()->make();
        $page->comments()->save($comment);
        $comment->refresh();

        $endpoints = ["/comment/$comment->id/archive", "/comment/$comment->id/unarchive"];

        foreach ($endpoints as $endpoint) {
            $resp = $this->actingAs($viewer)->put($endpoint);
            $this->assertPermissionError($resp);
        }

        $this->permissions->grantUserRolePermissions($viewer, ['comment-delete-all']);

        foreach ($endpoints as $endpoint) {
            $resp = $this->actingAs($viewer)->put($endpoint);
            $resp->assertOk();
        }

        $this->permissions->removeUserRolePermissions($viewer, ['comment-delete-all']);
        $this->permissions->grantUserRolePermissions($viewer, ['comment-update-all']);

        foreach ($endpoints as $endpoint) {
            $resp = $this->actingAs($viewer)->put($endpoint);
            $resp->assertOk();
        }
    }

    public function test_non_top_level_comments_cant_be_archived_or_unarchived()
    {
        $this->asAdmin();
        $page = $this->entities->page();

        $comment = Comment::factory()->make();
        $page->comments()->save($comment);
        $subComment = Comment::factory()->make(['parent_id' => $comment->id]);
        $page->comments()->save($subComment);
        $subComment->refresh();

        $resp = $this->putJson("/comment/$subComment->id/archive");
        $resp->assertStatus(400);

        $this->assertDatabaseHas('comments', [
            'id' => $subComment->id,
            'archived' => false,
        ]);

        $resp = $this->putJson("/comment/$subComment->id/unarchive");
        $resp->assertStatus(400);
    }

    public function test_scripts_cannot_be_injected_via_comment_html()
    {
        $page = $this->entities->page();

        $script = '<script>const a = "script";</script><script>const b = "sneakyscript";</script><p onclick="1">My lovely comment</p>';
        $this->asAdmin()->postJson("/comment/$page->id", [
            'html' => $script,
        ]);

        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($script, false);
        $pageView->assertDontSee('sneakyscript', false);
        $pageView->assertSee('<p>My lovely comment</p>', false);

        $comment = $page->comments()->first();
        $this->putJson("/comment/$comment->id", [
            'html' => $script . '<p>updated</p>',
        ]);

        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($script, false);
        $pageView->assertDontSee('sneakyscript', false);
        $pageView->assertSee('<p>My lovely comment</p><p>updated</p>');
    }

    public function test_scripts_are_removed_even_if_already_in_db()
    {
        $page = $this->entities->page();
        Comment::factory()->create([
            'html' => '<script>superbadscript</script><script>superbadscript</script><p onclick="superbadonclick">scriptincommentest</p>',
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
        $input = '<h1>Test</h1><p id="abc" href="beans">Content<a href="#cat" data-a="b">a</a><section>Hello</section><section>there</section></p>';
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

    public function test_comment_html_spans_are_cleaned()
    {
        $page = $this->entities->page();
        $input = '<p><span class="beans">Hello</span> do you have <span style="white-space: discard;">biscuits</span>?</p>';
        $expected = '<p><span>Hello</span> do you have <span>biscuits</span>?</p>';

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
}
