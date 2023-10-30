<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Support\Facades\Log;

class ErrorTest extends TestCase
{
    public function test_404_page_does_not_show_login()
    {
        // Due to middleware being handled differently this will not fail
        // if our custom, middleware-loaded handler fails but this is here
        // as a reminder and as a general check in the event of other issues.
        $editor = $this->users->editor();
        $editor->name = 'tester';
        $editor->save();

        $this->actingAs($editor);
        $notFound = $this->get('/fgfdngldfnotfound');
        $notFound->assertStatus(404);
        $notFound->assertDontSeeText('Log in');
        $notFound->assertSeeText('tester');
    }

    public function test_item_not_found_does_not_get_logged_to_file()
    {
        $this->actingAs($this->users->viewer());
        $handler = $this->withTestLogger();
        $book = $this->entities->book();

        // Ensure we're seeing errors
        Log::error('cat');
        $this->assertTrue($handler->hasErrorThatContains('cat'));

        $this->get('/books/arandomnotfouindbook');
        $this->get($book->getUrl('/chapter/arandomnotfouindchapter'));
        $this->get($book->getUrl('/chapter/arandomnotfouindpages'));

        $this->assertCount(1, $handler->getRecords());
    }

    public function test_access_to_non_existing_image_location_provides_404_response()
    {
        $resp = $this->actingAs($this->users->viewer())->get('/uploads/images/gallery/2021-05/anonexistingimage.png');
        $resp->assertStatus(404);
        $resp->assertSeeText('Image Not Found');
    }

    public function test_posts_above_php_limit_shows_friendly_error()
    {
        // Fake super large JSON request
        $resp = $this->asEditor()->call('GET', '/books', [], [], [], [
            'CONTENT_LENGTH' => '10000000000',
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $resp->assertStatus(413);
        $resp->assertJson(['error' => 'The server cannot receive the provided amount of data. Try again with less data or a smaller file.']);
    }
}
