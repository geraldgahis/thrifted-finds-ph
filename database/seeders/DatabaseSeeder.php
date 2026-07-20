<?php

namespace Database\Seeders;

use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1. Create your Admin Account
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Account',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // Or use Hash::make('password')
            'role' => 'admin',
        ]);

        // 2. Create 10 dummy customers
        User::factory(10)->create()->each(function ($user) {
            // Automatically create a blank profile for each dummy customer
            CustomerProfile::create([
                'user_id' => $user->id,
            ]);
        });

        // 2. Run the Categories and Brands Seeder FIRST
        $this->call(CategoriesAndBrandsSeeder::class);

        // 3. Run the Product Seeder SECOND
        $this->call(ProductSeeder::class);
    }
}
