<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            FacilitySeeder::class,
            AssetCategorySeeder::class,
            InventoryTypeSeeder::class,
            VendorSeeder::class,
        ]);

        // Add test data for development environment
        if (app()->environment('local')) {
            $this->call(TestDataSeeder::class);
        }
    }
}