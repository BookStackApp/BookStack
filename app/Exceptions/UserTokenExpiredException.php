<?php namespace BookStack\Exceptions;

class UserTokenExpiredException extends \Exception {

    public $userId;

    /**
     * UserTokenExpiredException constructor.
     * @param string $message
     * @param int $userId
     */
    public function __construct(string $message, int $userId)
    {
        $this->userId = $userId;
        parent::__construct($message);
    }


}