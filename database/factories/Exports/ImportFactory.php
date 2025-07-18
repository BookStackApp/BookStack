<?php

namespace Database\Factories\Exports;

use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ImportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \BookStack\Exports\Import::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'path' => 'uploads/files/imports/' . Str::random(10) . '.zip',
            'name' => $this->faker->words(3, true),
            'type' => 'book',
            'size' => rand(1, 1001),
            'metadata' => '{"name": "My book"}',
            'created_at' => User::factory(),
        ];
    }
}
