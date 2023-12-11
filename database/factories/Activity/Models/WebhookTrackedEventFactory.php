<?php

namespace Database\Factories\Activity\Models;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Webhook;
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
            'event'      => ActivityType::all()[array_rand(ActivityType::all())],
        ];
    }
}
