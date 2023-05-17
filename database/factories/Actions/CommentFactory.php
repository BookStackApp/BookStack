<?php

namespace Database\Factories\Actions;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \BookStack\Activity\Models\Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $text = $this->faker->paragraph(1);
        $html = '<p>' . $text . '</p>';

        return [
            'html'      => $html,
            'text'      => $text,
            'parent_id' => null,
        ];
    }
}
