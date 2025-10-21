<?php

namespace Database\Factories\Activity\Models;

use BookStack\Activity\WatchLevels;
use BookStack\Entities\Models\Book;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\BookStack\Activity\Models\Watch>
 */
class WatchFactory extends Factory
{
    protected $model = \BookStack\Activity\Models\Watch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $book = Book::factory()->create();

        return [
            'user_id' => User::factory(),
            'watchable_id' => $book->id,
            'watchable_type' => 'book',
            'level' => WatchLevels::NEW,
        ];
    }
}
