<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesAndBrandsSeeder extends Seeder
{
    public function run(): void
    {
        // --- CATEGORIES ---
        $categories = [
            'T-Shirts', 'Jackets & Coats', 'Hoodies & Sweatshirts', 
            'Pants & Trousers', 'Jeans', 'Shorts', 'Polo Shirts', 
            'Button Downs', 'Sweaters & Knits', 'Activewear', 
            'Sneakers', 'Boots & Shoes', 'Bags & Backpacks', 
            'Hats & Caps', 'Accessories'
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }

        // --- BRANDS ---
        $brands = [
            // The Essentials / Vintage
            'Vintage / Unbranded', 'Custom / Reworked',
            
            // Sportswear & Sneakers
            'Nike', 'Adidas', 'Puma', 'Reebok', 'New Balance', 
            'Under Armour', 'Asics', 'Converse', 'Vans', 'Fila', 'Champion',
            
            // Workwear & Outdoor
            'Carhartt', 'Dickies', 'The North Face', 'Patagonia', 
            'Columbia', 'Timberland', 'Arc\'teryx', 'Salomon',
            
            // Streetwear & Skate
            'Stussy', 'Supreme', 'BAPE', 'Palace', 'HUF', 'Thrasher', 
            'Obey', 'Billionaire Boys Club', 'KITH', 'Off-White',
            
            // Denim & Heritage
            'Levi\'s', 'Wrangler', 'Lee', 'Guess', 'Calvin Klein', 
            'Tommy Hilfiger', 'Polo Ralph Lauren', 'Nautica', 'Lacoste',
            
            // Fast Fashion / Mall Brands (Common in Ukay)
            'Uniqlo', 'Zara', 'H&M', 'Gap', 'Abercrombie & Fitch'
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate([
                'name' => $brand,
                'slug' => Str::slug($brand),
            ]);
        }
    }
}