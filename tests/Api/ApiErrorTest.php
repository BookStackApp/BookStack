<?php

namespace Tests\Api;

use BookStack\App\SystemApiController;
use BookStack\Exceptions\PrettyException;
use Tests\TestCase;

class ApiErrorTest extends TestCase
{
    use TestsApi;

    public function test_exception_detail_only_shown_in_debug_mode()
    {
        $mockController = $this->partialMock(SystemApiController::class);
        $mockController->shouldReceive('read')->andThrow(\InvalidArgumentException::class, 'Potentially sensitive data', 500);

        $resp = $this->actingAsApiEditor()->get('/api/system');
        $resp->assertStatus(500);
        $resp->assertDontSee('Potentially sensitive data', false);
        $resp->assertJsonPath('error.message', 'An error occurred');

        config(['app.debug' => true]);

        $resp = $this->actingAsApiEditor()->get('/api/system');
        $resp->assertStatus(500);
        $resp->assertJsonPath('error.message', 'Potentially sensitive data');
    }

    public function test_exception_message_when_model_not_found()
    {
        $resp = $this->actingAsApiEditor()->get('/api/books/123456789');
        $resp->assertStatus(404);
        $resp->assertSee('The requested resource could not be found.', false);
    }

    public function test_pretty_exception_messages_are_provided_in_non_debug_mode()
    {
        $mockController = $this->partialMock(SystemApiController::class);
        $exception = new PrettyException('Mr Error is here!');
        $exception->setSubtitle('Oh no!');
        $exception->setDetails('Something has really gone wrong');
        $mockController->shouldReceive('read')->andThrow($exception);

        $resp = $this->actingAsApiEditor()->get('/api/system');
        $resp->assertStatus(500);
        $resp->assertJson([
            'error' => [
                'message' => 'Mr Error is here!. Oh no!. Something has really gone wrong.'
            ]
        ]);
    }
}
