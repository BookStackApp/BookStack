<?php

namespace Tests\Entity;

use BookStack\Actions\Comment;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function test_add_comment()
    {
        $this->asAdmin();
        $page = Page::first();

        $comment = factory(Comment::class)->make(['parent_id' => 2]);
        $resp = $this->postJson("/comment/$page->id", $comment->getAttributes());

        $resp->assertStatus(200);
        $resp->assertSee($comment->text);

        $pageResp = $this->get($page->getUrl());
        $pageResp->assertSee($comment->text);

        $this->assertDatabaseHas('comments', [
            'local_id'    => 1,
            'entity_id'   => $page->id,
            'entity_type' => Page::newModelInstance()->getMorphClass(),
            'text'        => $comment->text,
            'parent_id'   => 2,
        ]);
    }

    public function test_comment_edit()
    {
        $this->asAdmin();
        $page = Page::first();

        $comment = factory(Comment::class)->make();
        $this->postJson("/comment/$page->id", $comment->getAttributes());

        $comment = $page->comments()->first();
        $newText = 'updated text content';
        $resp = $this->putJson("/comment/$comment->id", [
            'text' => $newText,
        ]);

        $resp->assertStatus(200);
        $resp->assertSee($newText);
        $resp->assertDontSee($comment->text);

        $this->assertDatabaseHas('comments', [
            'text'      => $newText,
            'entity_id' => $page->id,
        ]);
    }

    public function test_comment_delete()
    {
        $this->asAdmin();
        $page = Page::first();

        $comment = factory(Comment::class)->make();
        $this->postJson("/comment/$page->id", $comment->getAttributes());

        $comment = $page->comments()->first();

        $resp = $this->delete("/comment/$comment->id");
        $resp->assertStatus(200);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_comments_converts_markdown_input_to_html()
    {
        $page = Page::first();
        $this->asAdmin()->postJson("/comment/$page->id", [
            'text' => '# My Title',
        ]);

        $this->assertDatabaseHas('comments', [
            'entity_id'   => $page->id,
            'entity_type' => $page->getMorphClass(),
            'text'        => '# My Title',
            'html'        => "<h1>My Title</h1>\n",
        ]);

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee('<h1>My Title</h1>', false);
    }

    public function test_html_cannot_be_injected_via_comment_content()
    {
        $this->asAdmin();
        $page = Page::first();

        $script = '<script>const a = "script";</script>\n\n# sometextinthecomment';
        $this->postJson("/comment/$page->id", [
            'text' => $script,
        ]);

        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($script, false);
        $pageView->assertSee('sometextinthecomment');

        $comment = $page->comments()->first();
        $this->putJson("/comment/$comment->id", [
            'text' => $script . 'updated',
        ]);

        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($script, false);
        $pageView->assertSee('sometextinthecommentupdated');
    }
}
