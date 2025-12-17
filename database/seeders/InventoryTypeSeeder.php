<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Create inventory types
        DB::table('inventory_types')->insert([
            [
                'name' => 'Consumable',
                'code' => 'CONS',
                'description' => 'Items that are consumed during use',
                'is_consumable' => true,
                'is_trackable' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Spare Parts',
                'code' => 'SPARE',
                'description' => 'Spare parts for equipment maintenance',
                'is_consumable' => false,
                'is_trackable' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tools',
                'code' => 'TOOL',
                'description' => 'Maintenance tools and equipment',
                'is_consumable' => false,
                'is_trackable' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create units of measurement
        DB::table('units_of_measurement')->insert([
            [
                'name' => 'Piece',
                'symbol' => 'pc',
                'type' => 'count',
                'conversion_factor' => 1,
                'base_unit' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Meter',
                'symbol' => 'm',
                'type' => 'length',
                'conversion_factor' => 1,
                'base_unit' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kilogram',
                'symbol' => 'kg',
                'type' => 'weight',
                'conversion_factor' => 1,
                'base_unit' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Liter',
                'symbol' => 'L',
                'type' => 'volume',
                'conversion_factor' => 1,
                'base_unit' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create item categories
        DB::table('item_categories')->insert([
            [
                'name' => 'Electrical',
                'code' => 'ELEC',
                'description' => 'Electrical components and supplies',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mechanical',
                'code' => 'MECH',
                'description' => 'Mechanical components and supplies',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HVAC',
                'code' => 'HVAC',
                'description' => 'HVAC components and supplies',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Safety',
                'code' => 'SAFETY',
                'description' => 'Safety equipment and supplies',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}