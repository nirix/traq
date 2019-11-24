<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Traq\Project;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Traq\Status;
use Traq\Priority;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Project::class, function (Faker $faker) {
    return [
        'name' => $faker->title,
        'codename' => $faker->text,
        'slug' => $faker->unique()->slug,
        'description' => $faker->realText,
        'enable_wiki' => true,
        'created_at' => now(),
        'updated_at' => now(),
        'display_order' => $faker->randomNumber,
        'default_status_id' => function () {
            return factory(Status::class)->create()->id;
        },
        'default_priority_id' => function () {
            return factory(Priority::class)->create()->id;
        },
    ];
});
