<?php namespace BookStack\Auth\Access;

use BookStack\Auth\User;
use BookStack\Exceptions\SamlException;
use BookStack\Exceptions\SocialSignInAccountNotUsed;
use Illuminate\Support\Str;

class CasService extends ExternalAuthService
{
    protected $config;
    protected $registrationService;
    protected $user;

    /**
     * CasService constructor.
     */
    public function __construct(RegistrationService $registrationService, User $user)
    {
        $this->config = config('cas');
        $this->registrationService = $registrationService;
        $this->user = $user;
    }

    /**
     * Handle the login process on a CAS callback.
     * @throws SocialSignInAccountNotUsed
     */
    public function handleLoginCallback(string $userName, $attributes)
    {
        $isLoggedIn = auth()->check();

        if ($userName === null) {
            throw new SamlException(trans('errors.saml_no_email_address'));
        }

        if ($isLoggedIn) {
            throw new SamlException(trans('errors.saml_already_logged_in'), '/login');
        }

        // Get or register
        $user = $this->user->newQuery()
            ->where('external_auth_id', '=', $userName)
            ->first();

        print_r($attributes);

        if (is_null($user)) {
            $userData = [
                'name' => $attributes['name'],
                'email' => $userName,
                'password' => Str::random(32),
                'external_auth_id' => $userName,
            ];

            $user = $this->registrationService->registerUser($userData, null, false);
        }

        // Login
        if ($user === null) {
            throw new SamlException(trans('errors.saml_user_not_registered', ['name' => $userDetails['external_id']]), '/login');
        }

        if ($this->shouldSyncGroups()) {
            $groups = $attributes['groups'];

            if (!is_array($groups))
                $groups = array($groups);

            $this->syncWithGroups($user, $groups);
        }

        auth()->login($user);
        return $user;
    }

    protected function shouldSyncGroups(): bool
    {
        return $this->config['user_to_groups'] !== false;
    }
}
