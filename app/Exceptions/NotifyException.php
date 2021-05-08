<?php namespace BookStack\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;

class NotifyException extends Exception implements Responsable
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

    /**
     * Send the response for this type of exception.
     * @inheritdoc
     */
    public function toResponse($request)
    {
        $message = $this->getMessage();

        if (!empty($message)) {
            session()->flash('error', $message);
        }

        return redirect($this->redirectLocation);
    }
}
