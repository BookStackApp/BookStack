<?php

namespace Database\Factories\Entities\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \BookStack\Entities\Models\Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $html = '<p>'.implode('</p>', $this->faker->paragraphs(5)).'</p>';

        return [
            'name'           => $this->faker->sentence,
            'slug'           => Str::random(10),
            'html'           => $html,
            'text'           => strip_tags($html),
            'revision_count' => 1,
        ];
    }
}
