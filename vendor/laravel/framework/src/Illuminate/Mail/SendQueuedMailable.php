<?php

namespace Illuminate\Mail;

use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Contracts\Mail\Mailable as MailableContract;

class SendQueuedMailable
{
    /**
     * The mailable message instance.
     *
     * @var Mailable
     */
    public $mailable;

    /**
     * Create a new job instance.
     *
     * @param  \Illuminate\Contracts\Mail\Mailable  $mailable
     * @return void
     */
    public function __construct(MailableContract $mailable)
    {
        $this->mailable = $mailable;
    }

    /**
     * Handle the queued job.
     *
     * @param  \Illuminate\Contracts\Mail\Mailer  $mailer
     * @return void
     */
    public function handle(MailerContract $mailer)
    {
        $mailer->send($this->mailable);
    }

    /**
     * Get the display name for the queued job.
     *
     * @return string
     */
    public function displayName()
    {
        return get_class($this->mailable);
    }
}
