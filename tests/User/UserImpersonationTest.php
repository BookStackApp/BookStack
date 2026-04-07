<?php

namespace Tests\User;

use Tests\TestCase;

class UserImpersonationTest extends TestCase
{
    protected function tearDown(): void
    {
        session()->forget('impersonate');
        parent::tearDown();
    }

    public function test_impersonate_button_shown_on_edit_page_for_admin()
    {
        $viewer = $this->users->viewer();

        $resp = $this->asAdmin()->get("/settings/users/{$viewer->id}");

        $this->withHtml($resp)->assertElementExists("form[action$=\"/settings/users/{$viewer->id}/impersonate\"]");
        $resp->assertSee('Impersonate User');
    }

    public function test_impersonate_button_not_shown_for_non_admin()
    {
        $viewer = $this->users->viewer();
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get("/settings/users/{$viewer->id}");

        $resp->assertDontSee('Impersonate User');
        $this->withHtml($resp)->assertElementNotExists("form[action$=\"/settings/users/{$viewer->id}/impersonate\"]");
    }

    public function test_impersonate_button_not_shown_for_own_user()
    {
        $admin = $this->users->admin();

        $resp = $this->actingAs($admin)->get("/settings/users/{$admin->id}");

        $resp->assertDontSee('Impersonate User');
        $this->withHtml($resp)->assertElementNotExists("form[action$=\"/settings/users/{$admin->id}/impersonate\"]");
    }

    public function test_impersonate_button_not_shown_for_guest_user()
    {
        $guest = $this->users->guest();

        $resp = $this->asAdmin()->get("/settings/users/{$guest->id}");

        $resp->assertDontSee('Impersonate User');
        $this->withHtml($resp)->assertElementNotExists("form[action$=\"/settings/users/{$guest->id}/impersonate\"]");
    }

    public function test_impersonate_button_not_shown_when_already_impersonating()
    {
        $viewer = $this->users->viewer();
        $editor = $this->users->editor();

        $this->asAdmin()->post("/settings/users/{$viewer->id}/impersonate");

        $resp = $this->get("/settings/users/{$editor->id}");
        $resp->assertDontSee('Impersonate User');
    }

    public function test_impersonate_sets_session_and_redirects_to_home()
    {
        $viewer = $this->users->viewer();

        $resp = $this->asAdmin()->post("/settings/users/{$viewer->id}/impersonate");

        $resp->assertRedirect('/');
        $this->assertSessionHas('impersonate', $viewer->id);
    }

    public function test_impersonate_requires_users_manage_permission()
    {
        $viewer = $this->users->viewer();
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->post("/settings/users/{$viewer->id}/impersonate");

        $resp->assertRedirect('/');
        $this->assertSessionMissing('impersonate');
    }

    public function test_cannot_impersonate_guest_user()
    {
        $guest = $this->users->guest();

        $resp = $this->asAdmin()->post("/settings/users/{$guest->id}/impersonate");

        $resp->assertRedirect("/settings/users/{$guest->id}");
        $this->assertSessionError('You cannot impersonate this user');
        $this->assertSessionMissing('impersonate');
    }

    public function test_cannot_impersonate_self()
    {
        $admin = $this->users->admin();

        $resp = $this->actingAs($admin)->post("/settings/users/{$admin->id}/impersonate");

        $resp->assertRedirect("/settings/users/{$admin->id}");
        $this->assertSessionError('You cannot impersonate this user');
        $this->assertSessionMissing('impersonate');
    }

    public function test_impersonation_banner_shown_while_impersonating()
    {
        $viewer = $this->users->viewer();

        $this->asAdmin()->post("/settings/users/{$viewer->id}/impersonate");
        $resp = $this->get('/');

        $resp->assertSee('Impersonating: ' . $viewer->name);
        $resp->assertSee('Stop Impersonating');
    }

    public function test_impersonation_banner_not_shown_when_not_impersonating()
    {
        $resp = $this->asAdmin()->get('/');

        $resp->assertDontSee('Impersonating:');
        $resp->assertDontSee('Stop Impersonating');
    }

    public function test_requests_are_performed_as_impersonated_user()
    {
        $viewer = $this->users->viewer();
        $admin = $this->users->admin();

        $this->actingAs($admin)->post("/settings/users/{$viewer->id}/impersonate");

        $resp = $this->get('/');
        $resp->assertSee('Impersonating: ' . $viewer->name);
    }

    public function test_stop_impersonate_clears_session_and_redirects_to_user_edit()
    {
        $viewer = $this->users->viewer();

        $this->asAdmin()->post("/settings/users/{$viewer->id}/impersonate");
        $this->assertSessionHas('impersonate', $viewer->id);

        $resp = $this->get('/impersonate/stop');

        $resp->assertRedirect("/settings/users/{$viewer->id}");
        $this->assertSessionMissing('impersonate');
    }

    public function test_stop_impersonate_banner_gone_after_stopping()
    {
        $viewer = $this->users->viewer();

        $this->asAdmin()->post("/settings/users/{$viewer->id}/impersonate");
        $this->get('/impersonate/stop');

        $resp = $this->get('/');
        $resp->assertDontSee('Impersonating:');
    }

    public function test_middleware_does_not_switch_user_without_impersonate_session()
    {
        $admin = $this->users->admin();

        $resp = $this->actingAs($admin)->get('/');

        $resp->assertDontSee('Impersonating:');
    }

    public function test_middleware_does_not_switch_user_if_actor_lacks_users_manage()
    {
        $viewer = $this->users->viewer();
        $editor = $this->users->editor();

        $this->actingAs($editor)->withSession(['impersonate' => $viewer->id]);

        $resp = $this->get('/');

        $resp->assertDontSee('Impersonating: ' . $viewer->name);
    }

    public function test_stop_impersonate_link_shown_in_user_menu_while_impersonating()
    {
        $viewer = $this->users->viewer();

        $this->asAdmin()->post("/settings/users/{$viewer->id}/impersonate");
        $resp = $this->get('/');

        $this->withHtml($resp)->assertElementContains('a[href="' . url('/impersonate/stop') . '"]', 'Stop Impersonating');
    }
}
