<?php

namespace Database\Factories\Entities\Models;

use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageUpdateDraftFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \BookStack\Entities\Models\PageUpdateDraft::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $html = '<p>' . implode('</p>', $this->faker->paragraphs(5)) . '</p>';
        $page = Page::query()->first();

        return [
            'page_id'        => $page->id,
            'name'           => $this->faker->sentence(),
            'html'           => $html,
            'markdown'       => strip_tags($html),
            'summary'        => $this->faker->sentence(),
            'created_by'     => User::factory(),
        ];
    }
}
