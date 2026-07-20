<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $brands = Brand::all();

        // If we don't have categories or brands, abort so it doesn't crash
        if ($categories->isEmpty() || $brands->isEmpty()) {
            return;
        }

        $adjectives = ['Vintage 90s', 'Y2K', 'Faded', 'Distressed', 'Oversized', 'Deadstock'];
        $items = ['Windbreaker', 'Graphic Tee', 'Cargo Pants', 'Puffer Jacket', 'Zip-up Hoodie'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];

        for ($i = 0; $i < 50; $i++) {
            $brand = $brands->random();
            $category = $categories->random();
            
            $title = $adjectives[array_rand($adjectives)] . ' ' . $brand->name . ' ' . $items[array_rand($items)];

            Product::create([
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'title' => $title,
                'slug' => Str::slug($title . ' ' . Str::random(5)),
                'description' => 'Pre-loved premium thrift item. Minimal flaws.',
                'size_tag' => $sizes[array_rand($sizes)],
                'condition' => '9/10',
                'price' => rand(300, 2500),
                'status' => 'available',
            ]);
        }
    }
}