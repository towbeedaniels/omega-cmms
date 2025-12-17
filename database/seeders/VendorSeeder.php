<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vendors')->insert([
            [
                'name' => 'Electrical Supplies Inc.',
                'code' => 'ELECSUP',
                'contact_person' => 'John Smith',
                'contact_email' => 'john@electricalsupplies.com',
                'contact_phone' => '+1-555-1001',
                'website' => 'https://electricalsupplies.com',
                'payment_terms' => json_encode(['terms' => 'Net 30', 'discount' => '2% 10']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mechanical Parts Co.',
                'code' => 'MECHPARTS',
                'contact_person' => 'Sarah Johnson',
                'contact_email' => 'sarah@mechparts.com',
                'contact_phone' => '+1-555-1002',
                'website' => 'https://mechparts.com',
                'payment_terms' => json_encode(['terms' => 'Net 45', 'discount' => '1% 15']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HVAC Solutions Ltd.',
                'code' => 'HVACSOL',
                'contact_person' => 'Mike Wilson',
                'contact_email' => 'mike@hvacsolutions.com',
                'contact_phone' => '+1-555-1003',
                'website' => 'https://hvacsolutions.com',
                'payment_terms' => json_encode(['terms' => 'Net 60', 'discount' => '3% 20']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Safety First Equipment',
                'code' => 'SAFETYFIRST',
                'contact_person' => 'Lisa Brown',
                'contact_email' => 'lisa@safetyfirst.com',
                'contact_phone' => '+1-555-1004',
                'website' => 'https://safetyfirst.com',
                'payment_terms' => json_encode(['terms' => 'Net 30', 'discount' => '5% 10']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}