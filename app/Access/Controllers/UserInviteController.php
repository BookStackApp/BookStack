<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\UserInviteService;
use BookStack\Exceptions\NotifyException;
use BookStack\Exceptions\UserTokenExpiredException;
use BookStack\Exceptions\UserTokenNotFoundException;
use BookStack\Http\Controller;
use BookStack\Users\UserRepo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserInviteController extends Controller
{
    protected UserInviteService $inviteService;
    protected UserRepo $userRepo;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserInviteService $inviteService, UserRepo $userRepo)
    {
        $this->middleware('guest');
        $this->middleware('guard:standard');

        $this->inviteService = $inviteService;
        $this->userRepo = $userRepo;
    }

    /**
     * Show the page for the user to set the password for their account.
     *
     * @throws Exception
     */
    public function showSetPassword(string $token)
    {
        try {
            $this->inviteService->checkTokenAndGetUserId($token);
        } catch (Exception $exception) {
            return $this->handleTokenException($exception);
        }

        return view('auth.invite-set-password', [
            'token' => $token,
        ]);
    }

    /**
     * Sets the password for an invited user and then grants them access.
     *
     * @throws Exception
     */
    public function setPassword(Request $request, string $token)
    {
        $this->validate($request, [
            'password' => ['required', Password::default()],
        ]);

        try {
            $userId = $this->inviteService->checkTokenAndGetUserId($token);
        } catch (Exception $exception) {
            return $this->handleTokenException($exception);
        }

        $user = $this->userRepo->getById($userId);
        $user->password = Hash::make($request->get('password'));
        $user->email_confirmed = true;
        $user->save();

        $this->inviteService->deleteByUser($user);
        $this->showSuccessNotification(trans('auth.user_invite_success_login', ['appName' => setting('app-name')]));

        return redirect('/login');
    }

    /**
     * Check and validate the exception thrown when checking an invite token.
     *
     * @throws Exception
     *
     * @return never
     */
    protected function handleTokenException(Exception $exception)
    {
        throw match (get_class($exception)) {
            UserTokenNotFoundException::class => new NotifyException('', '/', 500, $exception),
            UserTokenExpiredException::class => new NotifyException(trans('errors.invite_token_expired'), '/password/email', 500, $exception),
            default => $exception,
        };
    }
}
