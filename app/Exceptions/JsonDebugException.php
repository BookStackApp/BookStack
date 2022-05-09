<?php

namespace BookStack\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class JsonDebugException extends Exception
{
    protected array $data;

    /**
     * JsonDebugException constructor.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        parent::__construct();
    }

    /**
     * Convert this exception into a response.
     * We add a manual data conversion to UTF8 to ensure any binary data is presentable as a JSON string.
     */
    public function render(): JsonResponse
    {
        $cleaned = mb_convert_encoding($this->data, 'UTF-8');

        return response()->json($cleaned);
    }
}
