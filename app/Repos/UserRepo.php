<?php namespace Oxbow\Repos;


use Oxbow\User;

class UserRepo
{

    protected $user;

    /**
     * UserRepo constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    public function getByEmail($email) {
        return $this->user->where('email', '=', $email)->first();
    }

    public function getById($id)
    {
        return $this->user->findOrFail($id);
    }
}