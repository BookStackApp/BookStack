<?php namespace Tests;

use BookStack\Page;
use BookStack\Comment;

class CommentTest extends TestCase
{

    public function test_add_comment()
    {
        $this->asAdmin();
        $page = Page::first();

        $comment = factory(Comment::class)->make(['parent_id' => 2]);
        $resp = $this->postJson("/ajax/page/$page->id/comment", $comment->getAttributes());

        $resp->assertStatus(200);
        $resp->assertSee($comment->text);

        $pageResp = $this->get($page->getUrl());
        $pageResp->assertSee($comment->text);

        $this->assertDatabaseHas('comments', [
            'local_id' => 1,
            'entity_id' => $page->id,
            'entity_type' => 'BookStack\\Page',
            'text' => $comment->text,
            'parent_id' => 2
        ]);
    }

    public function test_comment_edit()
    {
        $this->asAdmin();
        $page = Page::first();

        $comment = factory(Comment::class)->make();
        $this->postJson("/ajax/page/$page->id/comment", $comment->getAttributes());

        $comment = $page->comments()->first();
        $newText = 'updated text content';
        $resp = $this->putJson("/ajax/comment/$comment->id", [
            'text' => $newText,
            'html' => '<p>'.$newText.'</p>',
        ]);

        $resp->assertStatus(200);
        $resp->assertSee($newText);
        $resp->assertDontSee($comment->text);

        $this->assertDatabaseHas('comments', [
            'text' => $newText,
            'entity_id' => $page->id
        ]);
    }

    public function test_comment_delete()
    {
        $this->asAdmin();
        $page = Page::first();

        $comment = factory(Comment::class)->make();
        $this->postJson("/ajax/page/$page->id/comment", $comment->getAttributes());

        $comment = $page->comments()->first();

        $resp = $this->delete("/ajax/comment/$comment->id");
        $resp->assertStatus(200);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id
        ]);
    }
}
