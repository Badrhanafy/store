<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Create 5 new arrival products (created in the last 7 days)
        Product::factory()
            ->count(5)
            ->create(['created_at' => Carbon::now()->subDays(rand(0, 6))]);
            
        // Create older products
        Product::factory()
            ->count(15)
            ->create(['created_at' => Carbon::now()->subDays(rand(7, 365))]);
    }
}