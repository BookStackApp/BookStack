<?php

namespace BookStack\Exceptions;

use Illuminate\Contracts\Foundation\ExceptionRenderer;

class BookStackExceptionHandlerPage implements ExceptionRenderer
{
    public function render($throwable)
    {
        return view('errors.debug', [
            'error'       => $throwable->getMessage(),
            'errorClass'  => get_class($throwable),
            'trace'       => $throwable->getTraceAsString(),
            'environment' => $this->getEnvironment(),
        ])->render();
    }

    protected function safeReturn(callable $callback, $default = null)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $default;
        }
    }

    protected function getEnvironment(): array
    {
        return [
            'PHP Version'       => phpversion(),
            'BookStack Version' => $this->safeReturn(function () {
                $versionFile = base_path('version');

                return trim(file_get_contents($versionFile));
            }, 'unknown'),
            'Theme Configured' => $this->safeReturn(function () {
                return config('view.theme');
            }) ?? 'None',
        ];
    }
}
