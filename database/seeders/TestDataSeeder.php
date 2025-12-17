<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Only run in local environment
        if (!app()->environment('local')) {
            return;
        }

        $this->createTestAssets();
        $this->createTestInventory();
        $this->createTestWorkOrders();
    }

    private function createTestAssets(): void
    {
        // Create test assets with consistent column order
        DB::table('assets')->insert([
            // Row 1: Main Transformer
            [
                'facility_id' => 1,
                'zone_id' => 1,
                'model_id' => 1,
                'asset_tag' => 'ELEC-001',
                'serial_number' => 'SN-TRANS-001',
                'name' => 'Main Transformer',
                'status' => 'operational',
                'acquisition_date' => '2023-01-15',
                'acquisition_cost' => 50000.00,
                'warranty_expiry' => '2026-01-15',
                'installation_date' => '2023-02-01',
                'last_maintenance_date' => '2024-01-15',
                'next_maintenance_date' => '2024-04-15',
                'specifications' => json_encode(['location' => 'Basement', 'room' => 'Electrical Room A']),
                'is_critical' => true,
                'requires_calibration' => true,
                'last_calibration_date' => '2024-01-10',
                'next_calibration_date' => '2024-07-10',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Row 2: Main Switchgear - FIXED: added missing fields
            [
                'facility_id' => 1,
                'zone_id' => 2,
                'model_id' => 2,
                'asset_tag' => 'ELEC-002',
                'serial_number' => 'SN-SWGR-001',
                'name' => 'Main Switchgear',
                'status' => 'operational',
                'acquisition_date' => '2022-06-01',
                'acquisition_cost' => 75000.00,
                'warranty_expiry' => '2025-06-01',
                'installation_date' => '2022-06-15',
                'last_maintenance_date' => '2023-12-01',
                'next_maintenance_date' => '2024-06-01',
                'specifications' => json_encode(['location' => 'Basement', 'room' => 'Electrical Room B']),
                'is_critical' => true,
                'requires_calibration' => false,
                'last_calibration_date' => null, // Added missing field
                'next_calibration_date' => null, // Added missing field
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Row 3: Main Chiller - FIXED: corrected field order
            [
                'facility_id' => 1,
                'zone_id' => 3,
                'model_id' => 3,
                'asset_tag' => 'HVAC-001',
                'serial_number' => 'SN-CHLR-001',
                'name' => 'Main Chiller',
                'status' => 'under_maintenance',
                'acquisition_date' => '2021-03-15',
                'acquisition_cost' => 120000.00,
                'warranty_expiry' => '2024-03-15',
                'installation_date' => '2021-04-01',
                'last_maintenance_date' => '2023-09-01',
                'next_maintenance_date' => '2024-03-01',
                'specifications' => json_encode(['location' => 'Roof', 'room' => 'Mechanical Room']),
                'is_critical' => false,
                'requires_calibration' => true,
                'last_calibration_date' => '2023-08-15',
                'next_calibration_date' => '2024-02-15',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function createTestInventory(): void
    {
        // First, check if warehouse already exists
        if (!DB::table('warehouses')->where('code', 'WH-MAIN')->exists()) {
            DB::table('warehouses')->insert([
                [
                    'facility_id' => 1,
                    'name' => 'Main Warehouse',
                    'code' => 'WH-MAIN',
                    'location' => 'Building A, Basement',
                    'storage_capacity' => json_encode(['area' => '5000 sq ft', 'racks' => 50]),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Create test items if they don't exist
        $items = [
            [
                'code' => 'WIRE-10AWG',
                'inventory_type_id' => 1, // Consumable
                'category_id' => 1, // Electrical
                'unit_of_measurement_id' => 1, // pc
                'manufacturer_id' => 1,
                'name' => '10 AWG Electrical Wire',
                'description' => 'Copper electrical wire, 10 AWG',
                'part_number' => 'GE-WIRE-10',
                'specifications' => json_encode(['gauge' => '10 AWG', 'material' => 'Copper', 'insulation' => 'PVC']),
                'unit_cost' => 2.50,
                'average_cost' => 2.50,
                'last_purchase_cost' => 2.50,
                'current_stock' => 500,
                'minimum_stock' => 100,
                'maximum_stock' => 1000,
                'reorder_point' => 150,
                'lead_time_days' => 7,
                'is_active' => true,
            ],
            [
                'code' => 'BEARING-6205',
                'inventory_type_id' => 2, // Spare Parts
                'category_id' => 2, // Mechanical
                'unit_of_measurement_id' => 1, // pc
                'manufacturer_id' => 2,
                'name' => '6205 Bearing',
                'description' => 'Deep groove ball bearing, 6205 series',
                'part_number' => 'SIEM-BRG-6205',
                'specifications' => json_encode(['type' => 'Ball Bearing', 'size' => '25x52x15mm', 'speed' => '10000 RPM']),
                'unit_cost' => 15.75,
                'average_cost' => 15.75,
                'last_purchase_cost' => 15.75,
                'current_stock' => 25,
                'minimum_stock' => 10,
                'maximum_stock' => 50,
                'reorder_point' => 15,
                'lead_time_days' => 14,
                'is_active' => true,
            ],
            [
                'code' => 'SAFETY-GLASSES',
                'inventory_type_id' => 3, // Tools
                'category_id' => 4, // Safety
                'unit_of_measurement_id' => 1, // pc
                'manufacturer_id' => null,
                'name' => 'Safety Glasses',
                'description' => 'Anti-fog safety glasses with side shields',
                'part_number' => 'SF-GLASS-001',
                'specifications' => json_encode(['type' => 'Safety', 'material' => 'Polycarbonate', 'color' => 'Clear']),
                'unit_cost' => 8.99,
                'average_cost' => 8.99,
                'last_purchase_cost' => 8.99,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'reorder_point' => 30,
                'lead_time_days' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            if (!DB::table('items')->where('code', $item['code'])->exists()) {
                $item['created_at'] = now();
                $item['updated_at'] = now();
                DB::table('items')->insert([$item]);
            }
        }

        // Create inventory references if they don't exist
        if (!DB::table('inventory_references')->where('reference_code', 'REF-WIRE-001')->exists()) {
            DB::table('inventory_references')->insert([
                [
                    'item_id' => 1,
                    'supplier_id' => 1,
                    'reference_code' => 'REF-WIRE-001',
                    'manufacturer_part_number' => 'GE-WIRE-10',
                    'supplier_part_number' => 'ELEC-WIRE-10',
                    'minimum_order_quantity' => 100,
                    'supplier_price' => 2.25,
                    'supplier_currency' => 'USD',
                    'delivery_lead_time' => 5,
                    'delivery_terms' => 'FOB',
                    'is_preferred' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    private function createTestWorkOrders(): void
    {
        // Create test work orders if they don't exist
        $workOrders = [
            [
                'work_order_number' => 'WO-202412-0001',
                'asset_id' => 3, // Chiller
                'facility_id' => 1,
                'requested_by' => 1, // Admin
                'assigned_to' => 3, // Technician
                'title' => 'Chiller Maintenance',
                'description' => 'Routine preventive maintenance for main chiller',
                'priority' => 'medium',
                'status' => 'in_progress',
                'work_type' => 'preventive',
                'scheduled_date' => '2024-12-18',
                'due_date' => '2024-12-20',
                'estimated_hours' => 8.00,
                'estimated_cost' => 500.00,
                'safety_instructions' => 'Wear safety glasses and gloves. Lock out power before starting.',
                'requires_approval' => false,
                'is_approved' => true,
                'started_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'work_order_number' => 'WO-202412-0002',
                'asset_id' => 2, // Switchgear
                'facility_id' => 1,
                'requested_by' => 2, // Manager
                'title' => 'Switchgear Inspection',
                'description' => 'Monthly electrical inspection of main switchgear',
                'priority' => 'high',
                'status' => 'pending',
                'work_type' => 'preventive',
                'scheduled_date' => '2024-12-22',
                'due_date' => '2024-12-22',
                'estimated_hours' => 4.00,
                'estimated_cost' => 250.00,
                'requires_approval' => true,
                'is_approved' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'work_order_number' => 'WO-202411-0010',
                'asset_id' => 1, // Transformer
                'facility_id' => 1,
                'requested_by' => 2, // Manager
                'assigned_to' => 3, // Technician
                'title' => 'Transformer Oil Analysis',
                'description' => 'Annual oil analysis and testing for main transformer',
                'priority' => 'medium',
                'status' => 'completed',
                'work_type' => 'predictive',
                'scheduled_date' => '2024-11-15',
                'due_date' => '2024-11-15',
                'estimated_hours' => 6.00,
                'estimated_cost' => 400.00,
                'actual_hours' => 5.50,
                'actual_cost' => 375.00,
                'completed_at' => '2024-11-15 16:30:00',
                'created_at' => '2024-11-10 09:00:00',
                'updated_at' => '2024-11-15 16:30:00',
            ],
        ];

        foreach ($workOrders as $wo) {
            if (!DB::table('work_orders')->where('work_order_number', $wo['work_order_number'])->exists()) {
                DB::table('work_orders')->insert([$wo]);
            }
        }

        // Create work order completion for the completed work order
        if (!DB::table('work_order_completions')->where('work_order_id', 3)->exists()) {
            DB::table('work_order_completions')->insert([
                [
                    'work_order_id' => 3,
                    'completed_by' => 3, // Technician
                    'completed_at' => '2024-11-15 16:30:00',
                    'status' => 'completed',
                    'actual_hours' => 5.50,
                    'labor_cost' => 275.00,
                    'material_cost' => 100.00,
                    'notes' => 'Oil analysis completed successfully. All parameters within normal range.',
                    'parts_used' => json_encode([
                        ['item_id' => 1, 'quantity' => 2, 'unit_cost' => 2.50],
                        ['item_id' => 3, 'quantity' => 1, 'unit_cost' => 8.99]
                    ]),
                    'downtime_hours' => 0,
                    'customer_satisfaction' => true,
                    'created_at' => '2024-11-15 16:30:00',
                    'updated_at' => '2024-11-15 16:30:00',
                ],
            ]);
        }
    }
}