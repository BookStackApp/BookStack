<?php

namespace Tests\Activity;

use BookStack\Activity\Models\Comment;
use BookStack\Permissions\Permission;
use Tests\Api\TestsApi;
use Tests\TestCase;

class CommentsApiTest extends TestCase
{
    use TestsApi;

    public function test_endpoint_permission_controls()
    {
        $user = $this->users->editor();
        $this->permissions->grantUserRolePermissions($user, [Permission::CommentDeleteAll, Permission::CommentUpdateAll]);

        $page = $this->entities->page();
        $comment = Comment::factory()->make();
        $page->comments()->save($comment);
        $this->actingAsForApi($user);

        $actions = [
            ['GET', '/api/comments'],
            ['GET', "/api/comments/{$comment->id}"],
            ['POST', "/api/comments"],
            ['PUT', "/api/comments/{$comment->id}"],
            ['DELETE', "/api/comments/{$comment->id}"],
        ];

        foreach ($actions as [$method, $endpoint]) {
            $resp = $this->call($method, $endpoint);
            $this->assertNotPermissionError($resp);
        }

        $comment = Comment::factory()->make();
        $page->comments()->save($comment);
        $this->getJson("/api/comments")->assertSee(['id' => $comment->id]);

        $this->permissions->removeUserRolePermissions($user, [
            Permission::CommentDeleteAll, Permission::CommentDeleteOwn,
            Permission::CommentUpdateAll, Permission::CommentUpdateOwn,
            Permission::CommentCreateAll
        ]);

        $this->assertPermissionError($this->json('delete', "/api/comments/{$comment->id}"));
        $this->assertPermissionError($this->json('put', "/api/comments/{$comment->id}"));
        $this->assertPermissionError($this->json('post', "/api/comments"));
        $this->assertNotPermissionError($this->json('get', "/api/comments/{$comment->id}"));

        $this->permissions->disableEntityInheritedPermissions($page);
        $this->json('get', "/api/comments/{$comment->id}")->assertStatus(404);
        $this->getJson("/api/comments")->assertDontSee(['id' => $comment->id]);
    }

    public function test_index()
    {
        $page = $this->entities->page();
        Comment::query()->delete();

        $comments = Comment::factory()->count(10)->make();
        $page->comments()->saveMany($comments);

        $firstComment = $comments->first();
        $resp = $this->actingAsApiEditor()->getJson('/api/comments');
        $resp->assertJson([
            'data' => [
                [
                    'id' => $firstComment->id,
                    'commentable_id' => $page->id,
                    'commentable_type' => 'page',
                    'parent_id' => null,
                    'local_id' => $firstComment->local_id,
                ],
            ],
        ]);
        $resp->assertJsonCount(10, 'data');
        $resp->assertJson(['total' => 10]);

        $filtered = $this->getJson("/api/comments?filter[id]={$firstComment->id}");
        $filtered->assertJsonCount(1, 'data');
        $filtered->assertJson(['total' => 1]);
    }

    public function test_create()
    {
        $page = $this->entities->page();

        $resp = $this->actingAsApiEditor()->postJson('/api/comments', [
            'page_id' => $page->id,
            'html' => '<p>My wonderful comment</p>',
            'content_ref' => 'test-content-ref',
        ]);
        $resp->assertOk();
        $id = $resp->json('id');

        $this->assertDatabaseHas('comments', [
            'id' => $id,
            'commentable_id' => $page->id,
            'commentable_type' => 'page',
            'html' => '<p>My wonderful comment</p>',
        ]);

        $comment = Comment::query()->findOrFail($id);
        $this->assertIsInt($comment->local_id);

        $reply = $this->actingAsApiEditor()->postJson('/api/comments', [
            'page_id' => $page->id,
            'html' => '<p>My wonderful reply</p>',
            'content_ref' => 'test-content-ref',
            'reply_to' => $comment->local_id,
        ]);
        $reply->assertOk();

        $this->assertDatabaseHas('comments', [
            'id' => $reply->json('id'),
            'commentable_id' => $page->id,
            'commentable_type' => 'page',
            'html' => '<p>My wonderful reply</p>',
            'parent_id' => $comment->local_id,
        ]);
    }

    public function test_read()
    {
        $page = $this->entities->page();
        $user = $this->users->viewer();
        $comment = Comment::factory()->make([
            'html' => '<p>A lovely comment <script>hello</script></p>',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $page->comments()->save($comment);
        $comment->refresh();
        $reply = Comment::factory()->make([
            'parent_id' => $comment->local_id,
            'html' => '<p>A lovely<script>angry</script>reply</p>',
        ]);
        $page->comments()->save($reply);

        $resp = $this->actingAsApiEditor()->getJson("/api/comments/{$comment->id}");
        $resp->assertJson([
            'id' => $comment->id,
            'commentable_id' => $page->id,
            'commentable_type' => 'page',
            'html' => '<p>A lovely comment </p>',
            'archived' => false,
            'created_by' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'updated_by' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'replies' => [
                [
                    'id' => $reply->id,
                    'html' => '<p>A lovelyreply</p>'
                ]
            ]
        ]);
    }

    public function test_update()
    {
        $page = $this->entities->page();
        $user = $this->users->editor();
        $this->permissions->grantUserRolePermissions($user, [Permission::CommentUpdateAll]);
        $comment = Comment::factory()->make([
            'html' => '<p>A lovely comment</p>',
            'created_by' => $this->users->viewer()->id,
            'updated_by' => $this->users->viewer()->id,
            'parent_id' => null,
        ]);
        $page->comments()->save($comment);

        $this->actingAsForApi($user)->putJson("/api/comments/{$comment->id}", [
           'html' => '<p>A lovely updated comment</p>',
        ])->assertOk();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'html' => '<p>A lovely updated comment</p>',
            'archived' => 0,
        ]);

        $this->putJson("/api/comments/{$comment->id}", [
            'archived' => true,
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'html' => '<p>A lovely updated comment</p>',
            'archived' => 1,
        ]);

        $this->putJson("/api/comments/{$comment->id}", [
            'archived' => false,
            'html' => '<p>A lovely updated again comment</p>',
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'html' => '<p>A lovely updated again comment</p>',
            'archived' => 0,
        ]);
    }

    public function test_update_cannot_archive_replies()
    {
        $page = $this->entities->page();
        $user = $this->users->editor();
        $this->permissions->grantUserRolePermissions($user, [Permission::CommentUpdateAll]);
        $comment = Comment::factory()->make([
            'html' => '<p>A lovely comment</p>',
            'created_by' => $this->users->viewer()->id,
            'updated_by' => $this->users->viewer()->id,
            'parent_id' => 90,
        ]);
        $page->comments()->save($comment);

        $resp = $this->actingAsForApi($user)->putJson("/api/comments/{$comment->id}", [
            'archived' => true,
        ]);

        $this->assertEquals($this->errorResponse('Only top-level comments can be archived.', 400), $resp->json());
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'archived' => 0,
        ]);
    }

    public function test_destroy()
    {
        $page = $this->entities->page();
        $user = $this->users->editor();
        $this->permissions->grantUserRolePermissions($user, [Permission::CommentDeleteAll]);
        $comment = Comment::factory()->make([
            'html' => '<p>A lovely comment</p>',
        ]);
        $page->comments()->save($comment);

        $this->actingAsForApi($user)->deleteJson("/api/comments/{$comment->id}")->assertStatus(204);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }
}
