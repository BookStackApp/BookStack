<?php

namespace Database\Factories\Entities\Models;

use BookStack\Entities\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\BookStack\Entities\Models\SlugHistory>
 */
class SlugHistoryFactory extends Factory
{
    protected $model = \BookStack\Entities\Models\SlugHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sluggable_id' => Book::factory(),
            'sluggable_type' => 'book',
            'slug' => $this->faker->slug(),
            'parent_slug' => null,
        ];
    }
}
