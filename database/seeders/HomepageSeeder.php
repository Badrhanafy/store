<?php
// database/seeders/HomepageSeeder.php
namespace Database\Seeders;

use App\Models\Slide;
use App\Models\Category;
use Illuminate\Database\Seeder;

class HomepageSeeder extends Seeder
{
    public function run()
    {
        Slide::create([
            'title' => 'Summer Collection',
            'subtitle' => 'Discover our new arrivals',
            'cta' => 'Shop Now',
            'image' => 'https://example.com/summer.jpg',
            'link' => '/summer-collection',
            'bg_color' => 'bg-gradient-to-r from-amber-100 to-pink-200',
            'order' => 1,
            'is_active' => true
        ]);

        Category::create([
            'name' => '9obiyat',
            'image' => 'https://example.com/9obiyat.jpg',
            'link' => '/products/9obiyat',
            'order' => 1,
            'is_active' => true
        ]);
    }
}