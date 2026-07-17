<?php

namespace BookStack\Exceptions;

class ZipValidationException extends \Exception
{
    public function __construct(
        public array $errors
    ) {
        parent::__construct();
    }
}
