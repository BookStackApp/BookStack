<?php namespace BookStack\Exceptions;

class NotifyException extends \Exception
{

    public $message;
    public $redirectLocation;

    /**
     * NotifyException constructor.
     */
    public function __construct(string $message, string $redirectLocation = "/")
    {
        $this->message = $message;
        $this->redirectLocation = $redirectLocation;
        parent::__construct();
    }
}
