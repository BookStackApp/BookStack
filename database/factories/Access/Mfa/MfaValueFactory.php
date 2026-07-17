<?php

namespace Database\Factories\Access\Mfa;

use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\BookStack\Access\Mfa\MfaValue>
 */
class MfaValueFactory extends Factory
{
    protected $model = \BookStack\Access\Mfa\MfaValue::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'method' => 'totp',
            'value' => '123456',
        ];
    }
}
