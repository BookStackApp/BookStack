<?php

namespace BookStack\Exceptions;

use Exception;

class PermissionsException extends Exception implements ShowsApiExceptionMessage
{
    public function getMessageForApi(): string
    {
        return $this->getMessage();
    }
}
