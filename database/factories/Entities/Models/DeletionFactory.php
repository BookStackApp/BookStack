<?php

namespace Database\Factories\Entities\Models;

use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\BookStack\Entities\Models\Deletion>
 */
class DeletionFactory extends Factory
{
    protected $model = \BookStack\Entities\Models\Deletion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deleted_by' => User::factory(),
            'deletable_id' => Page::factory(),
            'deletable_type' => 'page',
        ];
    }
}
