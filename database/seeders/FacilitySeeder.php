<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        // Create clusters
        DB::table('clusters')->insert([
            [
                'id' => 1,
                'name' => 'Main Campus',
                'code' => 'MCAMP',
                'description' => 'Primary campus location',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'West Campus',
                'code' => 'WCAMP',
                'description' => 'Secondary campus location',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create regions
        DB::table('regions')->insert([
            [
                'id' => 1,
                'name' => 'North Region',
                'code' => 'NRGN',
                'timezone' => 'America/New_York',
                'contact_info' => json_encode(['phone' => '+1-555-0101', 'email' => 'north@company.com']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'South Region',
                'code' => 'SRGN',
                'timezone' => 'America/Chicago',
                'contact_info' => json_encode(['phone' => '+1-555-0102', 'email' => 'south@company.com']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create facilities
        DB::table('facilities')->insert([
            [
                'cluster_id' => 1,
                'region_id' => 1,
                'name' => 'Main Headquarters',
                'code' => 'MHQ',
                'type' => 'building',
                'color_code' => '#3B82F6',
                'address' => '123 Main St, Anytown, USA',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'specifications' => json_encode(['floors' => 10, 'area' => '50000 sq ft']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cluster_id' => 1,
                'region_id' => 1,
                'name' => 'Manufacturing Plant',
                'code' => 'MPLANT',
                'type' => 'infrastructure',
                'color_code' => '#10B981',
                'address' => '456 Industrial Ave, Anytown, USA',
                'latitude' => 40.7589,
                'longitude' => -73.9851,
                'specifications' => json_encode(['area' => '100000 sq ft', 'zoning' => 'industrial']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create buildings for facility 1
        DB::table('buildings')->insert([
            [
                'facility_id' => 1,
                'name' => 'Administration Building',
                'code' => 'ADMIN',
                'floors' => 5,
                'year_built' => 2010,
                'total_area' => 25000.00,
                'floor_plans' => json_encode(['floor1' => 'plan1.jpg', 'floor2' => 'plan2.jpg']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'facility_id' => 1,
                'name' => 'Research Center',
                'code' => 'RESEARCH',
                'floors' => 3,
                'year_built' => 2015,
                'total_area' => 15000.00,
                'floor_plans' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create zones for building 1
        DB::table('zones')->insert([
            [
                'building_id' => 1,
                'name' => 'First Floor',
                'code' => 'FLR1',
                'zone_type' => 'floor',
                'level' => '1',
                'boundaries' => null,
                'is_restricted' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'building_id' => 1,
                'name' => 'IT Department',
                'code' => 'ITDEPT',
                'zone_type' => 'department',
                'level' => '2',
                'boundaries' => null,
                'is_restricted' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'building_id' => 1,
                'name' => 'Executive Wing',
                'code' => 'EXECWING',
                'zone_type' => 'wing',
                'level' => '3',
                'boundaries' => null,
                'is_restricted' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}