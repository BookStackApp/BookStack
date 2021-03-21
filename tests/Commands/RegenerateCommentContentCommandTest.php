<?php namespace Tests\Commands;

use BookStack\Actions\Comment;
use Tests\TestCase;

class RegenerateCommentContentCommandTest extends TestCase
{
    public function test_regenerate_comment_content_command()
    {
        Comment::query()->forceCreate([
            'html' => 'some_old_content',
            'text' => 'some_fresh_content',
        ]);

        $this->assertDatabaseHas('comments', [
            'html' => 'some_old_content',
        ]);

        $exitCode = \Artisan::call('bookstack:regenerate-comment-content');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('comments', [
            'html' => 'some_old_content',
        ]);
        $this->assertDatabaseHas('comments', [
            'html' => "<p>some_fresh_content</p>\n",
        ]);
    }
}