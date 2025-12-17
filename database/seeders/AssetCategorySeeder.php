<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Create asset categories using DB facade
        DB::table('asset_categories')->insert([
            [
                'id' => 1,
                'name' => 'Electrical Equipment',
                'code' => 'ELEC',
                'description' => 'All electrical equipment and systems',
                'depreciation_years' => 10,
                'depreciation_rate' => 10.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Mechanical Equipment',
                'code' => 'MECH',
                'description' => 'All mechanical equipment and systems',
                'depreciation_years' => 15,
                'depreciation_rate' => 6.67,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'HVAC Systems',
                'code' => 'HVAC',
                'description' => 'Heating, Ventilation, and Air Conditioning systems',
                'depreciation_years' => 12,
                'depreciation_rate' => 8.33,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create subcategories - FIXED: All rows must have same number of columns
        DB::table('asset_subcategories')->insert([
            [
                'category_id' => 1,
                'name' => 'Transformers',
                'code' => 'TRANS',
                'description' => 'Electrical transformers',
                'requires_calibration' => true,
                'calibration_interval_days' => 365,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1,  // Added missing category_id
                'name' => 'Switchgear',
                'code' => 'SWGR',
                'description' => 'Electrical switchgear',
                'requires_calibration' => false,
                'calibration_interval_days' => null,  // Added null for consistency
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 3,
                'name' => 'Chillers',
                'code' => 'CHLR',
                'description' => 'HVAC chillers',
                'requires_calibration' => true,
                'calibration_interval_days' => 180,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create manufacturers
        DB::table('manufacturers')->insert([
            [
                'name' => 'General Electric',
                'code' => 'GE',
                'contact_email' => 'support@ge.com',
                'contact_phone' => '+1-800-626-2004',
                'website' => 'https://www.ge.com',
                'support_email' => 'techsupport@ge.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siemens',
                'code' => 'SIEMENS',
                'contact_email' => 'info@siemens.com',
                'contact_phone' => '+1-800-743-6367',
                'website' => 'https://www.siemens.com',
                'support_email' => 'support@siemens.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carrier',
                'code' => 'CARRIER',
                'contact_email' => 'support@carrier.com',
                'contact_phone' => '+1-800-227-7437',
                'website' => 'https://www.carrier.com',
                'support_email' => 'techsupport@carrier.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create models
        DB::table('asset_models')->insert([
            [
                'manufacturer_id' => 1,
                'subcategory_id' => 1,
                'name' => 'GE 9E Transformer',
                'model_number' => 'GE-9E-500KVA',
                'specifications' => json_encode([
                    'capacity' => '500 KVA',
                    'voltage' => '13.8kV/480V',
                    'frequency' => '60Hz',
                    'weight' => '2500 kg'
                ]),
                'maintenance_interval_days' => 90,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'manufacturer_id' => 2,
                'subcategory_id' => 2,
                'name' => 'Siemens 8DJH Switchgear',
                'model_number' => 'SIEM-8DJH-24KV',
                'specifications' => json_encode([
                    'voltage' => '24kV',
                    'current' => '1250A',
                    'type' => 'SF6 Insulated',
                    'dimensions' => '800x800x2200 mm'
                ]),
                'maintenance_interval_days' => 180,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'manufacturer_id' => 3,
                'subcategory_id' => 3,
                'name' => 'Carrier 30XA Chiller',
                'model_number' => 'CAR-30XA-400RT',
                'specifications' => json_encode([
                    'capacity' => '400 RT',
                    'cooling_type' => 'Water Cooled',
                    'compressor' => 'Centrifugal',
                    'efficiency' => '0.55 kW/RT'
                ]),
                'maintenance_interval_days' => 60,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}