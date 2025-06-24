<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 500),
            'qte' => $this->faker->numberBetween(0, 100),
            'category' => $this->faker->randomElement(['T-Shirts', 'Jeans', 'Dresses', 'Jackets', 'Shoes']),
            'sizes' => json_encode($this->faker->randomElements(['S', 'M', 'L', 'XL'], $this->faker->numberBetween(1, 4))),
            'colors' => json_encode($this->faker->randomElements(['Red', 'Blue', 'Black', 'White', 'Green'], $this->faker->numberBetween(1, 3))),
            'image' => 'products/' . $this->faker->image('public/storage/products', 640, 480, null, false),
        ];
    }
}