<?php

namespace BookStack\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;

class JsonDebugException extends Exception implements Responsable
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
    public function toResponse($request): JsonResponse
    {
        $cleaned = mb_convert_encoding($this->data, 'UTF-8');

        return response()->json($cleaned);
    }
}
