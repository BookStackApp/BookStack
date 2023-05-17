<?php

namespace Database\Factories\Auth;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \BookStack\Users\Models\Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'display_name'     => $this->faker->sentence(3),
            'description'      => $this->faker->sentence(10),
            'external_auth_id' => '',
        ];
    }
}
