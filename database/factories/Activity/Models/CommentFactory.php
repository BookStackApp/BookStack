<?php

namespace Database\Factories\Activity\Models;

use BookStack\Users\Models\User;
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
     * A static counter to provide a unique local_id for each comment.
     */
    protected static int $nextLocalId = 1000;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $text = $this->faker->paragraph(1);
        $html = '<p>' . $text . '</p>';
        $nextLocalId = static::$nextLocalId++;

        $user = User::query()->first();

        return [
            'html'      => $html,
            'parent_id' => null,
            'local_id'  => $nextLocalId,
            'content_ref' => '',
            'archived' => false,
            'created_by' => $user ?? User::factory(),
            'updated_by' => $user ?? User::factory(),
        ];
    }
}
