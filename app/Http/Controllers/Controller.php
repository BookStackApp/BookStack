<?php

namespace BookStack\Http\Controllers;

use BookStack\Facades\Activity;
use BookStack\Interfaces\Loggable;
use BookStack\Ownable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

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
     */
    public function setPageTitle(string $title)
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
     * Checks that the current user has the given permission otherwise throw an exception.
     */
    protected function checkPermission(string $permission): void
    {
        if (!user() || !user()->can($permission)) {
            $this->showPermissionError();
        }
    }

    /**
     * Check the current user's permissions against an ownable item otherwise throw an exception.
     */
    protected function checkOwnablePermission(string $permission, Ownable $ownable): void
    {
        if (!userCan($permission, $ownable)) {
            $this->showPermissionError();
        }
    }

    /**
     * Check if a user has a permission or bypass the permission
     * check if the given callback resolves true.
     */
    protected function checkPermissionOr(string $permission, callable $callback): void
    {
        if ($callback() !== true) {
            $this->checkPermission($permission);
        }
    }

    /**
     * Check if the current user has a permission or bypass if the provided user
     * id matches the current user.
     */
    protected function checkPermissionOrCurrentUser(string $permission, int $userId): void
    {
        $this->checkPermissionOr($permission, function () use ($userId) {
            return $userId === user()->id;
        });
    }

    /**
     * Send back a json error message.
     */
    protected function jsonError(string $messageText = "", int $statusCode = 500): JsonResponse
    {
        return response()->json(['message' => $messageText, 'status' => 'error'], $statusCode);
    }

    /**
     * Create a response that forces a download in the browser.
     */
    protected function downloadResponse(string $content, string $fileName): Response
    {
        return response()->make($content, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    /**
     * Show a positive, successful notification to the user on next view load.
     */
    protected function showSuccessNotification(string $message): void
    {
        session()->flash('success', $message);
    }

    /**
     * Show a warning notification to the user on next view load.
     */
    protected function showWarningNotification(string $message): void
    {
        session()->flash('warning', $message);
    }

    /**
     * Show an error notification to the user on next view load.
     */
    protected function showErrorNotification(string $message): void
    {
        session()->flash('error', $message);
    }

    /**
     * Log an activity in the system.
     * @param string|Loggable
     */
    protected function logActivity(string $type, $detail = ''): void
    {
        Activity::add($type, $detail);
    }

    /**
     * Get the validation rules for image files.
     */
    protected function getImageValidationRules(): string
    {
        return 'image_extension|no_double_extension|mimes:jpeg,png,gif,webp';
    }
}
