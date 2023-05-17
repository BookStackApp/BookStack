<?php

namespace BookStack\Access\Controllers;

use BookStack\Access\LoginService;
use BookStack\Activity\ActivityType;
use BookStack\Http\Controllers\Controller;
use BookStack\Users\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ResetPasswordController extends Controller
{
    protected LoginService $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->middleware('guest');
        $this->middleware('guard:standard');

        $this->loginService = $loginService;
    }

    /**
     * Display the password reset view for the given token.
     * If no token is present, display the link request form.
     */
    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
        $response = Password::broker()->reset($credentials, function (User $user, string $password) {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();

            $this->loginService->login($user, auth()->getDefaultDriver());
        });

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response === Password::PASSWORD_RESET
            ? $this->sendResetResponse()
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset.
     */
    protected function sendResetResponse(): RedirectResponse
    {
        $this->showSuccessNotification(trans('auth.reset_password_success'));
        $this->logActivity(ActivityType::AUTH_PASSWORD_RESET_UPDATE, user());

        return redirect('/');
    }

    /**
     * Get the response for a failed password reset.
     */
    protected function sendResetFailedResponse(Request $request, string $response): RedirectResponse
    {
        // We show invalid users as invalid tokens as to not leak what
        // users may exist in the system.
        if ($response === Password::INVALID_USER) {
            $response = Password::INVALID_TOKEN;
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
