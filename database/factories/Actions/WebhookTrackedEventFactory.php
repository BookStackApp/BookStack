<?php

namespace Database\Factories;

use BookStack\Actions\ActivityType;
use BookStack\Actions\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookTrackedEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'webhook_id' => Webhook::factory(),
            'event' => ActivityType::all()[array_rand(ActivityType::all())],
        ];
    }
}
