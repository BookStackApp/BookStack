<?php

namespace BookStack\Exceptions;

use Exception;

class ApiAuthException extends Exception
{

    /**
     * ApiAuthException constructor.
     */
    public function __construct($message, $code = 401)
    {
        parent::__construct($message, $code);
    }
}