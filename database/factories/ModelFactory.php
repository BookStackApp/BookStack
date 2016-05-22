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

$factory->define(BookStack\User::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => str_random(10),
        'remember_token' => str_random(10),
        'email_confirmed' => 1
    ];
});

$factory->define(BookStack\Book::class, function ($faker) {
    return [
        'name' => $faker->sentence,
        'slug' => str_random(10),
        'description' => $faker->paragraph
    ];
});

$factory->define(BookStack\Chapter::class, function ($faker) {
    return [
        'name' => $faker->sentence,
        'slug' => str_random(10),
        'description' => $faker->paragraph
    ];
});

$factory->define(BookStack\Page::class, function ($faker) {
    $html = '<p>' . implode('</p>', $faker->paragraphs(5)) . '</p>';
    return [
        'name' => $faker->sentence,
        'slug' => str_random(10),
        'html' => $html,
        'text' => strip_tags($html)
    ];
});

$factory->define(BookStack\Role::class, function ($faker) {
    return [
        'display_name' => $faker->sentence(3),
        'description' => $faker->sentence(10)
    ];
});

$factory->define(BookStack\Tag::class, function ($faker) {
    return [
        'name' => $faker->city,
        'value' => $faker->sentence(3)
    ];
});