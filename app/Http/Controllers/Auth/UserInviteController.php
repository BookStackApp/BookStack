<?php

namespace BookStack\Http\Controllers\Auth;

use BookStack\Auth\Access\UserInviteService;
use BookStack\Auth\UserRepo;
use BookStack\Exceptions\UserTokenExpiredException;
use BookStack\Exceptions\UserTokenNotFoundException;
use BookStack\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class UserInviteController extends Controller
{
    protected $inviteService;
    protected $userRepo;

    /**
     * Create a new controller instance.
     *
     * @param UserInviteService $inviteService
     * @param UserRepo $userRepo
     */
    public function __construct(UserInviteService $inviteService, UserRepo $userRepo)
    {
        $this->inviteService = $inviteService;
        $this->userRepo = $userRepo;
        $this->middleware('guest');
        parent::__construct();
    }

    /**
     * Show the page for the user to set the password for their account.
     * @param string $token
     * @return Factory|View|RedirectResponse
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
     * @param string $token
     * @param Request $request
     * @return RedirectResponse|Redirector
     * @throws Exception
     */
    public function setPassword(string $token, Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:8'
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
        session()->flash('success', trans('auth.user_invite_success', ['appName' => setting('app-name')]));
        $this->inviteService->deleteByUser($user);

        return redirect('/');
    }

    /**
     * Check and validate the exception thrown when checking an invite token.
     * @param Exception $exception
     * @return RedirectResponse|Redirector
     * @throws Exception
     */
    protected function handleTokenException(Exception $exception)
    {
        if ($exception instanceof UserTokenNotFoundException) {
            return redirect('/');
        }

        if ($exception instanceof UserTokenExpiredException) {
            session()->flash('error', trans('errors.invite_token_expired'));
            return redirect('/password/email');
        }

        throw $exception;
    }
}
