<?php

namespace BookStack\Http\Controllers;

use BookStack\Exceptions\NotifyException;
use BookStack\Facades\Activity;
use BookStack\Interfaces\Loggable;
use BookStack\Model;
use BookStack\Util\WebSafeMimeSniffer;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class Controller extends BaseController
{
    use DispatchesJobs;
    use ValidatesRequests;

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
     *
     * @return never
     */
    protected function showPermissionError()
    {
        $message = request()->wantsJson() ? trans('errors.permissionJson') : trans('errors.permission');

        throw new NotifyException($message, '/', 403);
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
    protected function checkOwnablePermission(string $permission, Model $ownable): void
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
    protected function jsonError(string $messageText = '', int $statusCode = 500): JsonResponse
    {
        return response()->json(['message' => $messageText, 'status' => 'error'], $statusCode);
    }

    /**
     * Create a response that forces a download in the browser.
     */
    protected function downloadResponse(string $content, string $fileName): Response
    {
        return response()->make($content, 200, [
            'Content-Type'           => 'application/octet-stream',
            'Content-Disposition'    => 'attachment; filename="' . str_replace('"', '', $fileName) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Create a response that forces a download, from a given stream of content.
     */
    protected function streamedDownloadResponse($stream, string $fileName): StreamedResponse
    {
        return response()->stream(function() use ($stream) {
            // End & flush the output buffer otherwise we still seem to use memory.
            // Ignore in testing since output buffers are used to gather a response.
            if (!app()->runningUnitTests()) {
                ob_end_clean();
            }

            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type'           => 'application/octet-stream',
            'Content-Disposition'    => 'attachment; filename="' . str_replace('"', '', $fileName) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Create a file download response that provides the file with a content-type
     * correct for the file, in a way so the browser can show the content in browser.
     */
    protected function inlineDownloadResponse(string $content, string $fileName): Response
    {
        $mime = (new WebSafeMimeSniffer())->sniff($content);

        return response()->make($content, 200, [
            'Content-Type'           => $mime,
            'Content-Disposition'    => 'inline; filename="' . str_replace('"', '', $fileName) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Create a file download response that provides the file with a content-type
     * correct for the file, in a way so the browser can show the content in browser,
     * for a given content stream.
     */
    protected function streamedInlineDownloadResponse($stream, string $fileName): StreamedResponse
    {
        $sniffContent = fread($stream, 1000);
        $mime = (new WebSafeMimeSniffer())->sniff($sniffContent);

        return response()->stream(function() use ($sniffContent, $stream) {
           echo $sniffContent;
           fpassthru($stream);
           fclose($stream);
        }, 200, [
            'Content-Type'           => $mime,
            'Content-Disposition'    => 'inline; filename="' . str_replace('"', '', $fileName) . '"',
            'X-Content-Type-Options' => 'nosniff',
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
     *
     * @param string|Loggable $detail
     */
    protected function logActivity(string $type, $detail = ''): void
    {
        Activity::add($type, $detail);
    }

    /**
     * Get the validation rules for image files.
     */
    protected function getImageValidationRules(): array
    {
        return ['image_extension', 'mimes:jpeg,png,gif,webp', 'max:' . (config('app.upload_limit') * 1000)];
    }
}
