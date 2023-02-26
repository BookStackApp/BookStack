<?php

namespace Database\Factories\Uploads;

use BookStack\Auth\User;
use BookStack\Entities\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\BookStack\Uploads\Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \BookStack\Uploads\Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(2, true),
            'path' => $this->faker->url(),
            'extension' => '',
            'external' => true,
            'uploaded_to' => Page::factory(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
            'order' => 0,
        ];
    }
}
