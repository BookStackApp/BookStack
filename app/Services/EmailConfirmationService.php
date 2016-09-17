<?php namespace BookStack\Services;

use BookStack\Notifications\ConfirmEmail;
use Carbon\Carbon;
use BookStack\EmailConfirmation;
use BookStack\Exceptions\ConfirmationEmailException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\User;

class EmailConfirmationService
{
    protected $emailConfirmation;

    /**
     * EmailConfirmationService constructor.
     * @param EmailConfirmation $emailConfirmation
     */
    public function __construct(EmailConfirmation $emailConfirmation)
    {
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
        $confirmation = $this->emailConfirmation->create([
            'user_id' => $user->id,
            'token'   => $token,
        ]);

        $confirmation->notify(new ConfirmEmail());
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