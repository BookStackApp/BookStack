<?php

namespace BookStack\Exceptions;

class ZipExportValidationException extends \Exception
{
    public function __construct(
        public array $errors,
    ) {
        parent::__construct();
    }
}
