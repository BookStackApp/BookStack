<?php

namespace Tests\Auth;

use BookStack\Access\Notifications\UserInviteNotification;
use BookStack\Access\UserInviteService;
use BookStack\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserInviteTest extends TestCase
{
    public function test_user_creation_creates_invite()
    {
        Notification::fake();
        $admin = $this->users->admin();

        $email = Str::random(16) . '@example.com';
        $resp = $this->actingAs($admin)->post('/settings/users/create', [
            'name'        => 'Barry',
            'email'       => $email,
            'send_invite' => 'true',
        ]);
        $resp->assertRedirect('/settings/users');

        $newUser = User::query()->where('email', '=', $email)->orderBy('id', 'desc')->first();

        Notification::assertSentTo($newUser, UserInviteNotification::class);
        $this->assertDatabaseHas('user_invites', [
            'user_id' => $newUser->id,
        ]);
    }

    public function test_user_invite_sent_in_selected_language()
    {
        Notification::fake();
        $admin = $this->users->admin();

        $email = Str::random(16) . '@example.com';
        $resp = $this->actingAs($admin)->post('/settings/users/create', [
            'name'        => 'Barry',
            'email'       => $email,
            'send_invite' => 'true',
            'language'    => 'de',
        ]);
        $resp->assertRedirect('/settings/users');

        $newUser = User::query()->where('email', '=', $email)->orderBy('id', 'desc')->first();
        Notification::assertSentTo($newUser, UserInviteNotification::class, function ($notification, $channels, $notifiable) {
            /** @var MailMessage $mail */
            $mail = $notification->toMail($notifiable);

            return 'Sie wurden eingeladen, BookStack beizutreten!' === $mail->subject &&
                'Ein Konto wurde für Sie auf BookStack erstellt.' === $mail->greeting;
        });
    }

    public function test_invite_set_password()
    {
        Notification::fake();
        $user = $this->users->viewer();
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
        $setPasswordResp->assertSee('Password set, you should now be able to login using your set password to access BookStack!');
        $newPasswordValid = auth()->validate([
            'email'    => $user->email,
            'password' => 'my test password',
        ]);
        $this->assertTrue($newPasswordValid);
        $this->assertDatabaseMissing('user_invites', [
            'user_id' => $user->id,
        ]);
    }

    public function test_invite_set_has_password_validation()
    {
        Notification::fake();
        $user = $this->users->viewer();
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
            'user_id' => $user->id,
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
        $user = $this->users->viewer();
        $inviteService = app(UserInviteService::class);

        $inviteService->sendInvitation($user);
        $tokenEntry = DB::table('user_invites')->where('user_id', '=', $user->id)->first();
        DB::table('user_invites')->update(['created_at' => Carbon::now()->subDays(14)->subHour(1)]);

        $setPasswordPageResp = $this->get('/register/invite/' . $tokenEntry->token);
        $setPasswordPageResp->assertRedirect('/password/email');
        $setPasswordPageResp->assertSessionHas('error', 'This invitation link has expired. You can instead try to reset your account password.');
    }
}
