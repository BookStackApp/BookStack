<?php

namespace Database\Factories\Activity\Models;

use BookStack\Entities\Models\Book;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\BookStack\Activity\Models\Favourite>
 */
class FavouriteFactory extends Factory
{
    protected $model = \BookStack\Activity\Models\Favourite::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $book = Book::query()->first();

        return [
            'user_id' => User::factory(),
            'favouritable_id' => $book->id,
            'favouritable_type' => 'book',
        ];
    }
}
