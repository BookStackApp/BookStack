<?php

namespace BookStack\Exceptions;

class ZipImportException extends \Exception
{
    public function __construct(
        public array $errors
    ) {
        parent::__construct();
    }
}
