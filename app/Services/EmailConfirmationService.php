<?php namespace BookStack\Services;


use Carbon\Carbon;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;
use BookStack\EmailConfirmation;
use BookStack\Exceptions\ConfirmationEmailException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Repos\UserRepo;
use BookStack\Setting;
use BookStack\User;

class EmailConfirmationService
{
    protected $mailer;
    protected $emailConfirmation;

    /**
     * EmailConfirmationService constructor.
     * @param Mailer            $mailer
     * @param EmailConfirmation $emailConfirmation
     */
    public function __construct(Mailer $mailer, EmailConfirmation $emailConfirmation)
    {
        $this->mailer = $mailer;
        $this->emailConfirmation = $emailConfirmation;
    }

    /**
     * Create new confirmation for a user,
     * Also removes any existing old ones.
     * @param User $user
     * @throws ConfirmationEmailException
     */
    public function sendConfirmation(User $user)
    {
        if ($user->email_confirmed) {
            throw new ConfirmationEmailException('Email has already been confirmed, Try logging in.', '/login');
        }
        $this->deleteConfirmationsByUser($user);
        $token = $this->getToken();
        $this->emailConfirmation->create([
            'user_id' => $user->id,
            'token'   => $token,
        ]);
        $this->mailer->send('emails/email-confirmation', ['token' => $token], function (Message $message) use ($user) {
            $appName = setting('app-name', 'BookStack');
            $message->to($user->email, $user->name)->subject('Confirm your email on ' . $appName . '.');
        });
    }

    /**
     * Gets an email confirmation by looking up the token,
     * Ensures the token has not expired.
     * @param string $token
     * @return EmailConfirmation
     * @throws UserRegistrationException
     */
    public function getEmailConfirmationFromToken($token)
    {
        $emailConfirmation = $this->emailConfirmation->where('token', '=', $token)->first();
        // If not found
        if ($emailConfirmation === null) {
            throw new UserRegistrationException('This confirmation token is not valid or has already been used, Please try registering again.', '/register');
        }

        // If more than a day old
        if (Carbon::now()->subDay()->gt($emailConfirmation->created_at)) {
            $this->sendConfirmation($emailConfirmation->user);
            throw new UserRegistrationException('The confirmation token has expired, A new confirmation email has been sent.', '/register/confirm');
        }

        return $emailConfirmation;
    }


    /**
     * Delete all email confirmations that belong to a user.
     * @param User $user
     * @return mixed
     */
    public function deleteConfirmationsByUser(User $user)
    {
        return $this->emailConfirmation->where('user_id', '=', $user->id)->delete();
    }

    /**
     * Creates a unique token within the email confirmation database.
     * @return string
     */
    protected function getToken()
    {
        $token = str_random(24);
        while ($this->emailConfirmation->where('token', '=', $token)->exists()) {
            $token = str_random(25);
        }
        return $token;
    }


}