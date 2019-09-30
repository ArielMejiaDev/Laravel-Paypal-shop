<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    $prices = [25.00, 50.00, 75.00, 100.00];
    return [
        'name'  =>  "T-shirt{$faker->city}",
        'price' =>  $prices[rand(0, 3)],
    ];
});
