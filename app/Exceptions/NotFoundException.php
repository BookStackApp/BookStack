<?php namespace BookStack\Exceptions;

class NotFoundException extends PrettyException
{

    /**
     * NotFoundException constructor.
     * @param string $message
     */
    public function __construct($message = 'Item not found')
    {
        parent::__construct($message, 404);
    }
}
