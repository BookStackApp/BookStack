<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\UserInviteService;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\UserTokenExpiredException;
use BookStack\Exceptions\UserTokenNotFoundException;
use BookStack\Facades\Theme;
use BookStack\Http\Controllers\Controller;
use BookStack\Theming\ThemeEvents;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class UserInviteController extends Controller
{
    protected $inviteService;
    protected $userRepo;

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
            'password' => 'required|min:8',
        ]);

        try {
            $userId = $this->inviteService->checkTokenAndGetUserId($token);
        } catch (Exception $exception) {
            return $this->handleTokenException($exception);
        }

        $user = $this->userRepo->getById($userId);
        $user->password = bcrypt($request->get('password'));
        $user->email_confirmed = true;
        $user->save();

        auth()->login($user);
        Theme::dispatch(ThemeEvents::AUTH_LOGIN, auth()->getDefaultDriver(), $user);
        $this->logActivity(ActivityType::AUTH_LOGIN, $user);
        $this->showSuccessNotification(trans('auth.user_invite_success', ['appName' => setting('app-name')]));
        $this->inviteService->deleteByUser($user);

        return redirect('/');
    }

    /**
     * Check and validate the exception thrown when checking an invite token.
     *
     * @throws Exception
     *
     * @return RedirectResponse|Redirector
     */
    protected function handleTokenException(Exception $exception)
    {
        if ($exception instanceof UserTokenNotFoundException) {
            return redirect('/');
        }

        if ($exception instanceof UserTokenExpiredException) {
            $this->showErrorNotification(trans('errors.invite_token_expired'));

            return redirect('/password/email');
        }

        throw $exception;
    }
}
