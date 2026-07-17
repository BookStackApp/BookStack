<?php

namespace Database\Factories\Sorting;

use BookStack\Sorting\SortRule;
use BookStack\Sorting\SortRuleOperation;
use Illuminate\Database\Eloquent\Factories\Factory;

class SortRuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SortRule::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $cases = SortRuleOperation::cases();
        $op = $cases[array_rand($cases)];
        return [
            'name' => $op->name . ' Sort',
            'sequence' => $op->value,
        ];
    }
}
