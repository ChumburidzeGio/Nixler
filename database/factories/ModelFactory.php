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

$factory->define(App\Entities\User::class, function (Faker\Generator $faker) {

    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'username' => $faker->uuid,
        'country' => 'GE',
        'locale' => 'ka',
        'currency' => 'GEL',
        'timezone' => 'Asia/Tbilisi',
    ];
});

$factory->define(App\Entities\Product::class, function (Faker\Generator $faker) {

	return [
		'title' => $faker->sentence,
		'description' => $faker->text,
		'price' => $faker->randomFloat(2, 0, 5000),
		'original_price' => $faker->optional(0.4)->randomFloat(2, 0, 5000),
		'in_stock' => $faker->randomDigitNotNull,
		'buy_link' => $faker->optional(0.05)->url,
		'category_id' => rand(1,79),
		'is_active' => $faker->boolean(90),
		'sku' => $faker->ean8,
	];

});

$factory->define(App\Entities\ProductVariant::class, function (Faker\Generator $faker) {

	return [
		'name' => $faker->colorName,
		'price' => $faker->randomFloat(2, 0, 5000),
		'original_price' => $faker->optional(0.4)->randomFloat(2, 0, 5000),
		'in_stock' => $faker->randomDigitNotNull,
	];

});
