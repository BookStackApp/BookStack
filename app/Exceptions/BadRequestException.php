<?php namespace BookStack\Exceptions;

class BadRequestException extends PrettyException
{

    /**
     * BadRequestException constructor.
     * @param string $message
     */
    public function __construct($message = 'Bad request')
    {
        parent::__construct($message, 400);
    }
}
