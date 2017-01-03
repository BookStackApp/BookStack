<?php namespace BookStack\Services;

use BookStack\Notifications\ConfirmEmail;
use BookStack\Repos\UserRepo;
use Carbon\Carbon;
use BookStack\Exceptions\ConfirmationEmailException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\User;
use Illuminate\Database\Connection as Database;

class EmailConfirmationService
{
    protected $db;
    protected $users;

    /**
     * EmailConfirmationService constructor.
     * @param Database $db
     * @param UserRepo $users
     */
    public function __construct(Database $db, UserRepo $users)
    {
        $this->db = $db;
        $this->users = $users;
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
            throw new ConfirmationEmailException(trans('errors.email_already_confirmed'), '/login');
        }

        $this->deleteConfirmationsByUser($user);
        $token = $this->createEmailConfirmation($user);

        $user->notify(new ConfirmEmail($token));
    }

    /**
     * Creates a new email confirmation in the database and returns the token.
     * @param User $user
     * @return string
     */
    public function createEmailConfirmation(User $user)
    {
        $token = $this->getToken();
        $this->db->table('email_confirmations')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        return $token;
    }

    /**
     * Gets an email confirmation by looking up the token,
     * Ensures the token has not expired.
     * @param string $token
     * @return array|null|\stdClass
     * @throws UserRegistrationException
     */
    public function getEmailConfirmationFromToken($token)
    {
        $emailConfirmation = $this->db->table('email_confirmations')->where('token', '=', $token)->first();

        // If not found show error
        if ($emailConfirmation === null) {
            throw new UserRegistrationException(trans('errors.email_confirmation_invalid'), '/register');
        }

        // If more than a day old
        if (Carbon::now()->subDay()->gt(new Carbon($emailConfirmation->created_at))) {
            $user = $this->users->getById($emailConfirmation->user_id);
            $this->sendConfirmation($user);
            throw new UserRegistrationException(trans('errors.email_confirmation_expired'), '/register/confirm');
        }

        $emailConfirmation->user = $this->users->getById($emailConfirmation->user_id);
        return $emailConfirmation;
    }

    /**
     * Delete all email confirmations that belong to a user.
     * @param User $user
     * @return mixed
     */
    public function deleteConfirmationsByUser(User $user)
    {
        return $this->db->table('email_confirmations')->where('user_id', '=', $user->id)->delete();
    }

    /**
     * Creates a unique token within the email confirmation database.
     * @return string
     */
    protected function getToken()
    {
        $token = str_random(24);
        while ($this->db->table('email_confirmations')->where('token', '=', $token)->exists()) {
            $token = str_random(25);
        }
        return $token;
    }


}