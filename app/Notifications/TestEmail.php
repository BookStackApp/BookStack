<?php namespace BookStack\Notifications;

class TestEmail extends MailNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return $this->newMailMessage()
                ->subject(trans('settings.maint_send_test_email_mail_subject'))
                ->greeting(trans('settings.maint_send_test_email_mail_greeting'))
                ->line(trans('settings.maint_send_test_email_mail_text'));
    }
}
