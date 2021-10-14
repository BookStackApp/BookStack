<?php

namespace BookStack\Exceptions;

use Whoops\Handler\Handler;

class WhoopsBookStackPrettyHandler extends Handler
{

    /**
     * @return int|null A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle()
    {
        // TODO - Assistance View
        // Docs links
        // Discord Links
        // Github Issue Links (With pre-filled search?)

        $exception = $this->getException();
        echo view('errors.debug', [
            'error' => $exception->getMessage(),
            'errorClass' => get_class($exception),
            'trace' => $exception->getTraceAsString(),
            'environment' => $this->getEnvironment(),
        ])->render();
        return Handler::QUIT;
    }

    protected function safeReturn(callable $callback, $default = null) {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $default;
        }
    }

    protected function getEnvironment(): array
    {
        return [
            'PHP Version' => phpversion(),
            'BookStack Version' => $this->safeReturn(function() {
                $versionFile = base_path('version');
                return trim(file_get_contents($versionFile));
            }, 'unknown'),
            'Theme Configured' => $this->safeReturn(function() {
                    return config('view.theme');
                }) ?? 'None',
        ];
    }
}