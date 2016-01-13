<?php

namespace BookStack\Providers;


use BookStack\Role;
use BookStack\Services\LdapService;
use BookStack\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class LdapUserProvider implements UserProvider
{

    /**
     * The user model.
     *
     * @var string
     */
    protected $model;

    /**
     * @var LdapService
     */
    protected $ldapService;


    /**
     * LdapUserProvider constructor.
     * @param             $model
     * @param LdapService $ldapService
     */
    public function __construct($model, LdapService $ldapService)
    {
        $this->model = $model;
        $this->ldapService = $ldapService;
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');
        return new $class;
    }


    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->createModel()->newQuery()->find($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->where($model->getRememberTokenName(), $token)
            ->first();
    }


    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string                                     $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        $user->save();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // Get user via LDAP
        $userDetails = $this->ldapService->getUserDetails($credentials['username']);
        if ($userDetails === null) return null;

        // Search current user base by looking up a uid
        $model = $this->createModel();
        $currentUser = $model->newQuery()
            ->where('external_auth_id', $userDetails['uid'])
            ->first();

        if ($currentUser !== null) return $currentUser;

        $model->name = $userDetails['name'];
        $model->external_auth_id = $userDetails['uid'];
        $model->email = $userDetails['email'];
        return $model;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array                                      $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->ldapService->validateUserCredentials($user, $credentials['username'], $credentials['password']);
    }
}
