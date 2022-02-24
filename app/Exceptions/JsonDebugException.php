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
     * Covert this exception into a response.
     */
    public function render(): JsonResponse
    {
        return response()->json($this->data);
    }
}
