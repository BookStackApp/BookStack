<?php namespace BookStack\Exceptions;

use Exception;

class JsonDebugException extends Exception
{

    protected $data;

    /**
     * JsonDebugException constructor.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Covert this exception into a response.
     */
    public function render()
    {
        return response()->json($this->data);
    }
}
