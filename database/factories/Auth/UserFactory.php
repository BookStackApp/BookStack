<?php

namespace Database\Factories\Auth;

use BookStack\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->name;

        return [
            'name'            => $name,
            'email'           => $this->faker->email,
            'slug'            => Str::slug($name . '-' . Str::random(5)),
            'password'        => Str::random(10),
            'remember_token'  => Str::random(10),
            'email_confirmed' => 1,
        ];
    }
}
