<?php

namespace Database\Factories\Access;

use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\BookStack\Access\SocialAccount>
 */
class SocialAccountFactory extends Factory
{
    protected $model = \BookStack\Access\SocialAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'driver' => 'github',
            'driver_id' => '123456',
            'avatar' => '',
        ];
    }
}
