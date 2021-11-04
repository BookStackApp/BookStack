<?php

namespace Database\Factories\Entities\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $html = '<p>' . implode('</p>', $this->faker->paragraphs(5)) . '</p>';

        return [
            'name'           => $this->faker->sentence,
            'slug'           => Str::random(10),
            'html'           => $html,
            'text'           => strip_tags($html),
            'revision_count' => 1,
        ];
    }
}
