<?php

namespace Database\Factories\Api;

use BookStack\Api\ApiToken;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApiTokenFactory extends Factory
{
    protected $model = ApiToken::class;

    public function definition(): array
    {
        return [
            'token_id' => Str::random(10),
            'secret' => Str::random(12),
            'name' => $this->faker->name(),
            'expires_at' => Carbon::now()->addYear(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::factory(),
        ];
    }
}
