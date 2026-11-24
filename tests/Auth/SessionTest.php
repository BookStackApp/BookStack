<?php

namespace Tests\Auth;

use Tests\TestCase;

class SessionTest extends TestCase
{
    public function test_session_refresh_page_redirects_to_login_if_guest_user()
    {
        $this->setSettings(['app-public' => 'true']);

        $resp = $this->get('/session/refresh');
        $resp->assertRedirect('/login?session_refresh=true');
    }

    public function test_session_refresh_page_show()
    {
        $resp = $this->asEditor()->get('/session/refresh');
        $resp->assertStatus(200);
        $resp->assertSee('Refreshing session...');
        $this->withHtml($resp)->assertElementExists('input[id="csrf_token"][type="hidden"]');
    }

    public function test_login_with_session_refresh_param_force_redirects_to_session_refresh_page()
    {
        $editor = $this->users->editor();
        $editor->password = bcrypt('password');
        $editor->save();

        $this->get('/login?session_refresh=true')->assertOk();

        $resp = $this->post('/login', ['email' => $editor->email, 'password' => 'password']);
        $resp->assertRedirect('/session/refresh');
    }
}
