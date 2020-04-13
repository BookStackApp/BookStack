<?php

namespace BookStack\Http\Controllers;

use BookStack\Ownable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Check if the current user is signed in.
     */
    protected function isSignedIn(): bool
    {
        return auth()->check();
    }

    /**
     * Stops the application and shows a permission error if
     * the application is in demo mode.
     */
    protected function preventAccessInDemoMode()
    {
        if (config('app.env') === 'demo') {
            $this->showPermissionError();
        }
    }

    /**
     * Adds the page title into the view.
     * @param $title
     */
    public function setPageTitle($title)
    {
        view()->share('pageTitle', $title);
    }

    /**
     * On a permission error redirect to home and display.
     * the error as a notification.
     */
    protected function showPermissionError()
    {
        if (request()->wantsJson()) {
            $response = response()->json(['error' => trans('errors.permissionJson')], 403);
        } else {
            $response = redirect('/');
            $this->showErrorNotification(trans('errors.permission'));
        }

        throw new HttpResponseException($response);
    }

    /**
     * Checks for a permission.
     * @param string $permissionName
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    protected function checkPermission($permissionName)
    {
        if (!user() || !user()->can($permissionName)) {
            $this->showPermissionError();
        }
        return true;
    }

    /**
     * Check the current user's permissions against an ownable item.
     * @param $permission
     * @param Ownable $ownable
     * @return bool
     */
    protected function checkOwnablePermission($permission, Ownable $ownable)
    {
        if (userCan($permission, $ownable)) {
            return true;
        }
        return $this->showPermissionError();
    }

    /**
     * Check if a user has a permission or bypass if the callback is true.
     * @param $permissionName
     * @param $callback
     * @return bool
     */
    protected function checkPermissionOr($permissionName, $callback)
    {
        $callbackResult = $callback();
        if ($callbackResult === false) {
            $this->checkPermission($permissionName);
        }
        return true;
    }

    /**
     * Check if the current user has a permission or bypass if the provided user
     * id matches the current user.
     * @param string $permissionName
     * @param int $userId
     * @return bool
     */
    protected function checkPermissionOrCurrentUser(string $permissionName, int $userId)
    {
        return $this->checkPermissionOr($permissionName, function () use ($userId) {
            return $userId === user()->id;
        });
    }

    /**
     * Send back a json error message.
     * @param string $messageText
     * @param int $statusCode
     * @return mixed
     */
    protected function jsonError($messageText = "", $statusCode = 500)
    {
        return response()->json(['message' => $messageText, 'status' => 'error'], $statusCode);
    }

    /**
     * Create the response for when a request fails validation.
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $errors
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if ($request->expectsJson()) {
            return response()->json(['validation' => $errors], 422);
        }

        return redirect()->to($this->getRedirectUrl())
            ->withInput($request->input())
            ->withErrors($errors, $this->errorBag());
    }

    /**
     * Create a response that forces a download in the browser.
     * @param string $content
     * @param string $fileName
     * @return \Illuminate\Http\Response
     */
    protected function downloadResponse(string $content, string $fileName)
    {
        return response()->make($content, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    /**
     * Show a positive, successful notification to the user on next view load.
     * @param string $message
     */
    protected function showSuccessNotification(string $message)
    {
        session()->flash('success', $message);
    }

    /**
     * Show a warning notification to the user on next view load.
     * @param string $message
     */
    protected function showWarningNotification(string $message)
    {
        session()->flash('warning', $message);
    }

    /**
     * Show an error notification to the user on next view load.
     * @param string $message
     */
    protected function showErrorNotification(string $message)
    {
        session()->flash('error', $message);
    }

    /**
     * Get the validation rules for image files.
     */
    protected function getImageValidationRules(): string
    {
        return 'image_extension|no_double_extension|mimes:jpeg,png,gif,webp';
    }
}
