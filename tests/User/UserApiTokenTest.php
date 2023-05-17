<?php

namespace Tests\User;

use BookStack\Activity\ActivityType;
use BookStack\Api\ApiToken;
use Carbon\Carbon;
use Tests\TestCase;

class UserApiTokenTest extends TestCase
{
    protected $testTokenData = [
        'name'       => 'My test API token',
        'expires_at' => '2050-04-01',
    ];

    public function test_tokens_section_not_visible_without_access_api_permission()
    {
        $user = $this->users->viewer();

        $resp = $this->actingAs($user)->get($user->getEditUrl());
        $resp->assertDontSeeText('API Tokens');

        $this->permissions->grantUserRolePermissions($user, ['access-api']);

        $resp = $this->actingAs($user)->get($user->getEditUrl());
        $resp->assertSeeText('API Tokens');
        $resp->assertSeeText('Create Token');
    }

    public function test_those_with_manage_users_can_view_other_user_tokens_but_not_create()
    {
        $viewer = $this->users->viewer();
        $editor = $this->users->editor();
        $this->permissions->grantUserRolePermissions($viewer, ['users-manage']);

        $resp = $this->actingAs($viewer)->get($editor->getEditUrl());
        $resp->assertSeeText('API Tokens');
        $resp->assertDontSeeText('Create Token');
    }

    public function test_create_api_token()
    {
        $editor = $this->users->editor();

        $resp = $this->asAdmin()->get($editor->getEditUrl('/create-api-token'));
        $resp->assertStatus(200);
        $resp->assertSee('Create API Token');
        $resp->assertSee('Token Secret');

        $resp = $this->post($editor->getEditUrl('/create-api-token'), $this->testTokenData);
        $token = ApiToken::query()->latest()->first();
        $resp->assertRedirect($editor->getEditUrl('/api-tokens/' . $token->id));
        $this->assertDatabaseHas('api_tokens', [
            'user_id'    => $editor->id,
            'name'       => $this->testTokenData['name'],
            'expires_at' => $this->testTokenData['expires_at'],
        ]);

        // Check secret token
        $this->assertSessionHas('api-token-secret:' . $token->id);
        $secret = session('api-token-secret:' . $token->id);
        $this->assertDatabaseMissing('api_tokens', [
            'secret' => $secret,
        ]);
        $this->assertTrue(\Hash::check($secret, $token->secret));

        $this->assertTrue(strlen($token->token_id) === 32);
        $this->assertTrue(strlen($secret) === 32);

        $this->assertSessionHas('success');
        $this->assertActivityExists(ActivityType::API_TOKEN_CREATE);
    }

    public function test_create_with_no_expiry_sets_expiry_hundred_years_away()
    {
        $editor = $this->users->editor();
        $this->asAdmin()->post($editor->getEditUrl('/create-api-token'), ['name' => 'No expiry token', 'expires_at' => '']);
        $token = ApiToken::query()->latest()->first();

        $over = Carbon::now()->addYears(101);
        $under = Carbon::now()->addYears(99);
        $this->assertTrue(
            ($token->expires_at < $over && $token->expires_at > $under),
            'Token expiry set at 100 years in future'
        );
    }

    public function test_created_token_displays_on_profile_page()
    {
        $editor = $this->users->editor();
        $this->asAdmin()->post($editor->getEditUrl('/create-api-token'), $this->testTokenData);
        $token = ApiToken::query()->latest()->first();

        $resp = $this->get($editor->getEditUrl());
        $this->withHtml($resp)->assertElementExists('#api_tokens');
        $this->withHtml($resp)->assertElementContains('#api_tokens', $token->name);
        $this->withHtml($resp)->assertElementContains('#api_tokens', $token->token_id);
        $this->withHtml($resp)->assertElementContains('#api_tokens', $token->expires_at->format('Y-m-d'));
    }

    public function test_secret_shown_once_after_creation()
    {
        $editor = $this->users->editor();
        $resp = $this->asAdmin()->followingRedirects()->post($editor->getEditUrl('/create-api-token'), $this->testTokenData);
        $resp->assertSeeText('Token Secret');

        $token = ApiToken::query()->latest()->first();
        $this->assertNull(session('api-token-secret:' . $token->id));

        $resp = $this->get($editor->getEditUrl('/api-tokens/' . $token->id));
        $resp->assertDontSeeText('Client Secret');
    }

    public function test_token_update()
    {
        $editor = $this->users->editor();
        $this->asAdmin()->post($editor->getEditUrl('/create-api-token'), $this->testTokenData);
        $token = ApiToken::query()->latest()->first();
        $updateData = [
            'name'       => 'My updated token',
            'expires_at' => '2011-01-01',
        ];

        $resp = $this->put($editor->getEditUrl('/api-tokens/' . $token->id), $updateData);
        $resp->assertRedirect($editor->getEditUrl('/api-tokens/' . $token->id));

        $this->assertDatabaseHas('api_tokens', array_merge($updateData, ['id' => $token->id]));
        $this->assertSessionHas('success');
        $this->assertActivityExists(ActivityType::API_TOKEN_UPDATE);
    }

    public function test_token_update_with_blank_expiry_sets_to_hundred_years_away()
    {
        $editor = $this->users->editor();
        $this->asAdmin()->post($editor->getEditUrl('/create-api-token'), $this->testTokenData);
        $token = ApiToken::query()->latest()->first();

        $resp = $this->put($editor->getEditUrl('/api-tokens/' . $token->id), [
            'name'       => 'My updated token',
            'expires_at' => '',
        ]);
        $token->refresh();

        $over = Carbon::now()->addYears(101);
        $under = Carbon::now()->addYears(99);
        $this->assertTrue(
            ($token->expires_at < $over && $token->expires_at > $under),
            'Token expiry set at 100 years in future'
        );
    }

    public function test_token_delete()
    {
        $editor = $this->users->editor();
        $this->asAdmin()->post($editor->getEditUrl('/create-api-token'), $this->testTokenData);
        $token = ApiToken::query()->latest()->first();

        $tokenUrl = $editor->getEditUrl('/api-tokens/' . $token->id);

        $resp = $this->get($tokenUrl . '/delete');
        $resp->assertSeeText('Delete Token');
        $resp->assertSeeText($token->name);
        $this->withHtml($resp)->assertElementExists('form[action="' . $tokenUrl . '"]');

        $resp = $this->delete($tokenUrl);
        $resp->assertRedirect($editor->getEditUrl('#api_tokens'));
        $this->assertDatabaseMissing('api_tokens', ['id' => $token->id]);
        $this->assertActivityExists(ActivityType::API_TOKEN_DELETE);
    }

    public function test_user_manage_can_delete_token_without_api_permission_themselves()
    {
        $viewer = $this->users->viewer();
        $editor = $this->users->editor();
        $this->permissions->grantUserRolePermissions($editor, ['users-manage']);

        $this->asAdmin()->post($viewer->getEditUrl('/create-api-token'), $this->testTokenData);
        $token = ApiToken::query()->latest()->first();

        $resp = $this->actingAs($editor)->get($viewer->getEditUrl('/api-tokens/' . $token->id));
        $resp->assertStatus(200);
        $resp->assertSeeText('Delete Token');

        $resp = $this->actingAs($editor)->delete($viewer->getEditUrl('/api-tokens/' . $token->id));
        $resp->assertRedirect($viewer->getEditUrl('#api_tokens'));
        $this->assertDatabaseMissing('api_tokens', ['id' => $token->id]);
    }
}
