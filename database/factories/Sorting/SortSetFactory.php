<?php

namespace Database\Factories\Sorting;

use BookStack\Sorting\SortSet;
use BookStack\Sorting\SortSetOperation;
use Illuminate\Database\Eloquent\Factories\Factory;

class SortSetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SortSet::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $cases = SortSetOperation::cases();
        $op = $cases[array_rand($cases)];
        return [
            'name' => $op->name . ' Sort',
            'sequence' => $op->value,
        ];
    }
}
