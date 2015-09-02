<?php

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

$factory->define(Oxbow\User::class, function ($faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->email,
        'password'       => str_random(10),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Oxbow\Book::class, function ($faker) {
    return [
        'name'        => $faker->sentence,
        'description' => $faker->paragraph
    ];
});

$factory->define(Oxbow\Chapter::class, function ($faker) {
    return [
        'name'        => $faker->sentence,
        'description' => $faker->paragraph
    ];
});

$factory->define(Oxbow\Page::class, function ($faker) {
    return [
        'name' => $faker->sentence,
        'html' => '<p>' . implode('</p>', $faker->paragraphs(5)) . '</p>'
    ];
});