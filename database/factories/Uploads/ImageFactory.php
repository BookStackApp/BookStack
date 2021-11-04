<?php

namespace Database\Factories\Uploads;

use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \BookStack\Uploads\Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'        => $this->faker->slug . '.jpg',
            'url'         => $this->faker->url,
            'path'        => $this->faker->url,
            'type'        => 'gallery',
            'uploaded_to' => 0,
        ];
    }
}
