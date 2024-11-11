<?php

namespace BookStack\Exceptions;

class ZipImportException extends \Exception
{
    public function __construct(
        public array $errors
    ) {
        $message = "Import failed with errors:" . implode("\n", $this->errors);
        parent::__construct($message);
    }
}
