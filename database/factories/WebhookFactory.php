<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'My webhook for ' . $this->faker->country(),
            'endpoint' => $this->faker->url,
        ];
    }
}
