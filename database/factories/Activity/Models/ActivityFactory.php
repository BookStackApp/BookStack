<?php

namespace Database\Factories\Activity\Models;

use BookStack\Activity\ActivityType;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\BookStack\Activity\Models\Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = \BookStack\Activity\Models\Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activities = ActivityType::all();
        $activity = $activities[array_rand($activities)];
        return [
            'type' => $activity,
            'detail' => 'Activity detail for ' . $activity,
            'user_id' => User::factory(),
            'ip' => $this->faker->ipv4(),
            'loggable_id' => null,
            'loggable_type' => null,
        ];
    }
}
