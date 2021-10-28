<?php
namespace Database\Factories\Auth;
use Illuminate\Database\Eloquent\Factories\Factory;


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \BookStack\Auth\User::class;

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
            'slug'            => \Illuminate\Support\Str::slug($name.'-'.\Illuminate\Support\Str::random(5)),
            'password'        => Str::random(10),
            'remember_token'  => Str::random(10),
            'email_confirmed' => 1,
        ];
    }
}
