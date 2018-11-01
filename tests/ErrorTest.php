<?php namespace Tests;

class ErrorTest extends TestCase
{

    public function test_404_page_does_not_show_login()
    {
        // Due to middleware being handled differently this will not fail
        // if our custom, middleware-loaded handler fails but this is here
        // as a reminder and as a general check in the event of other issues.
        $editor = $this->getEditor();
        $editor->name = 'tester';
        $editor->save();

        $this->actingAs($editor);
        $notFound = $this->get('/fgfdngldfnotfound');
        $notFound->assertStatus(404);
        $notFound->assertDontSeeText('Log in');
        $notFound->assertSeeText('tester');
    }
}