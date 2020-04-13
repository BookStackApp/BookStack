<?php namespace Tests\Auth;


use BookStack\Auth\Access\UserInviteService;
use BookStack\Auth\User;
use BookStack\Notifications\UserInvite;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
use Notification;
use Tests\TestCase;

class UserInviteTest extends TestCase
{

    public function test_user_creation_creates_invite()
    {
        Notification::fake();
        $admin = $this->getAdmin();

        $this->actingAs($admin)->post('/settings/users/create', [
            'name' => 'Barry',
            'email' => 'tester@example.com',
            'send_invite' => 'true',
        ]);

        $newUser = User::query()->where('email', '=', 'tester@example.com')->orderBy('id', 'desc')->first();

        Notification::assertSentTo($newUser, UserInvite::class);
        $this->assertDatabaseHas('user_invites', [
            'user_id' => $newUser->id
        ]);
    }

    public function test_invite_set_password()
    {
        Notification::fake();
        $user = $this->getViewer();
        $inviteService = app(UserInviteService::class);

        $inviteService->sendInvitation($user);
        $token = DB::table('user_invites')->where('user_id', '=', $user->id)->first()->token;

        $setPasswordPageResp = $this->get('/register/invite/' . $token);
        $setPasswordPageResp->assertSuccessful();
        $setPasswordPageResp->assertSee('Welcome to BookStack!');
        $setPasswordPageResp->assertSee('Password');
        $setPasswordPageResp->assertSee('Confirm Password');

        $setPasswordResp = $this->followingRedirects()->post('/register/invite/' . $token, [
            'password' => 'my test password',
        ]);
        $setPasswordResp->assertSee('Password set, you now have access to BookStack!');
        $newPasswordValid = auth()->validate([
            'email' => $user->email,
            'password' => 'my test password'
        ]);
        $this->assertTrue($newPasswordValid);
        $this->assertDatabaseMissing('user_invites', [
            'user_id' => $user->id
        ]);
    }

    public function test_invite_set_has_password_validation()
    {
        Notification::fake();
        $user = $this->getViewer();
        $inviteService = app(UserInviteService::class);

        $inviteService->sendInvitation($user);
        $token = DB::table('user_invites')->where('user_id', '=', $user->id)->first()->token;

        $this->get('/register/invite/' . $token);
        $shortPassword = $this->followingRedirects()->post('/register/invite/' . $token, [
            'password' => 'mypassw',
        ]);
        $shortPassword->assertSee('The password must be at least 8 characters.');

        $this->get('/register/invite/' . $token);
        $noPassword = $this->followingRedirects()->post('/register/invite/' . $token, [
            'password' => '',
        ]);
        $noPassword->assertSee('The password field is required.');

        $this->assertDatabaseHas('user_invites', [
            'user_id' => $user->id
        ]);
    }

    public function test_non_existent_invite_token_redirects_to_home()
    {
        $setPasswordPageResp = $this->get('/register/invite/' . Str::random(12));
        $setPasswordPageResp->assertRedirect('/');

        $setPasswordResp = $this->post('/register/invite/' . Str::random(12), ['password' => 'Password Test']);
        $setPasswordResp->assertRedirect('/');
    }

    public function test_token_expires_after_two_weeks()
    {
        Notification::fake();
        $user = $this->getViewer();
        $inviteService = app(UserInviteService::class);

        $inviteService->sendInvitation($user);
        $tokenEntry = DB::table('user_invites')->where('user_id', '=', $user->id)->first();
        DB::table('user_invites')->update(['created_at' => Carbon::now()->subDays(14)->subHour(1)]);

        $setPasswordPageResp = $this->get('/register/invite/' . $tokenEntry->token);
        $setPasswordPageResp->assertRedirect('/password/email');
        $setPasswordPageResp->assertSessionHas('error', 'This invitation link has expired. You can instead try to reset your account password.');
    }


}