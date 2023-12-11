<?php

namespace BookStack\Util;

use BookStack\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * Create a handler which runs the provided actions upon an
 * out-of-memory event. This allows reserving of memory to allow
 * the desired action to run as needed.
 *
 * Essentially provides a wrapper and memory reserving around the
 * memory handling added to the default app error handler.
 */
class OutOfMemoryHandler
{
    protected $onOutOfMemory;
    protected string $memoryReserve = '';

    public function __construct(callable $onOutOfMemory, int $memoryReserveMB = 4)
    {
        $this->onOutOfMemory = $onOutOfMemory;

        $this->memoryReserve = str_repeat('x', $memoryReserveMB * 1_000_000);
        $this->getHandler()->prepareForOutOfMemory(function () {
            return $this->handle();
        });
    }

    protected function handle(): mixed
    {
        $result = null;
        $this->memoryReserve = '';

        if ($this->onOutOfMemory) {
            $result = call_user_func($this->onOutOfMemory);
            $this->forget();
        }

        return $result;
    }

    /**
     * Forget the handler so no action is taken place on out of memory.
     */
    public function forget(): void
    {
        $this->memoryReserve = '';
        $this->onOutOfMemory = null;
        $this->getHandler()->forgetOutOfMemoryHandler();
    }

    protected function getHandler(): Handler
    {
        return app()->make(ExceptionHandler::class);
    }
}
