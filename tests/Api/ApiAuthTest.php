<?php

namespace Tests;

use BookStack\Auth\Permissions\RolePermission;
use BookStack\Auth\User;
use Carbon\Carbon;

class ApiAuthTest extends TestCase
{
    use TestsApi;

    protected $endpoint = '/api/books';

    public function test_requests_succeed_with_default_auth()
    {
        $viewer = $this->getViewer();
        $this->giveUserPermissions($viewer, ['access-api']);

        $resp = $this->get($this->endpoint);
        $resp->assertStatus(401);

        $this->actingAs($viewer, 'web');

        $resp = $this->get($this->endpoint);
        $resp->assertStatus(200);
    }

    public function test_no_token_throws_error()
    {
        $resp = $this->get($this->endpoint);
        $resp->assertStatus(401);
        $resp->assertJson($this->errorResponse("No authorization token found on the request", 401));
    }

    public function test_bad_token_format_throws_error()
    {
        $resp = $this->get($this->endpoint, ['Authorization' => "Token abc123"]);
        $resp->assertStatus(401);
        $resp->assertJson($this->errorResponse("An authorization token was found on the request but the format appeared incorrect", 401));
    }

    public function test_token_with_non_existing_id_throws_error()
    {
        $resp = $this->get($this->endpoint, ['Authorization' => "Token abc:123"]);
        $resp->assertStatus(401);
        $resp->assertJson($this->errorResponse("No matching API token was found for the provided authorization token", 401));
    }

    public function test_token_with_bad_secret_value_throws_error()
    {
        $resp = $this->get($this->endpoint, ['Authorization' => "Token {$this->apiTokenId}:123"]);
        $resp->assertStatus(401);
        $resp->assertJson($this->errorResponse("The secret provided for the given used API token is incorrect", 401));
    }

    public function test_api_access_permission_required_to_access_api()
    {
        $resp = $this->get($this->endpoint, $this->apiAuthHeader());
        $resp->assertStatus(200);
        auth()->logout();

        $accessApiPermission = RolePermission::getByName('access-api');
        $editorRole = $this->getEditor()->roles()->first();
        $editorRole->detachPermission($accessApiPermission);

        $resp = $this->get($this->endpoint, $this->apiAuthHeader());
        $resp->assertStatus(403);
        $resp->assertJson($this->errorResponse("The owner of the used API token does not have permission to make API calls", 403));
    }

    public function test_api_access_permission_required_to_access_api_with_session_auth()
    {
        $editor = $this->getEditor();
        $this->actingAs($editor, 'web');

        $resp = $this->get($this->endpoint);
        $resp->assertStatus(200);
        auth('web')->logout();

        $accessApiPermission = RolePermission::getByName('access-api');
        $editorRole = $this->getEditor()->roles()->first();
        $editorRole->detachPermission($accessApiPermission);

        $editor = User::query()->where('id', '=', $editor->id)->first();

        $this->actingAs($editor, 'web');
        $resp = $this->get($this->endpoint);
        $resp->assertStatus(403);
        $resp->assertJson($this->errorResponse("The owner of the used API token does not have permission to make API calls", 403));
    }

    public function test_token_expiry_checked()
    {
        $editor = $this->getEditor();
        $token = $editor->apiTokens()->first();

        $resp = $this->get($this->endpoint, $this->apiAuthHeader());
        $resp->assertStatus(200);
        auth()->logout();

        $token->expires_at = Carbon::now()->subDay()->format('Y-m-d');
        $token->save();

        $resp = $this->get($this->endpoint, $this->apiAuthHeader());
        $resp->assertJson($this->errorResponse("The authorization token used has expired", 403));
    }

    public function test_email_confirmation_checked_using_api_auth()
    {
        $editor = $this->getEditor();
        $editor->email_confirmed = false;
        $editor->save();

        // Set settings and get user instance
        $this->setSettings(['registration-enabled' => 'true', 'registration-confirmation' => 'true']);

        $resp = $this->get($this->endpoint, $this->apiAuthHeader());
        $resp->assertStatus(401);
        $resp->assertJson($this->errorResponse("The email address for the account in use needs to be confirmed", 401));
    }

}