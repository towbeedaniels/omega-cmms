<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Check if users already exist to avoid duplicates
        if (User::where('email', 'admin@omegacmms.com')->exists()) {
            echo "Admin user already exists. Skipping...\n";
        } else {
            // Create admin user
            User::create([
                'name' => 'System Administrator',
                'email' => 'admin@omegacmms.com',
                'password' => Hash::make('Admin@123'),
                'role_id' => 1,
                'employee_id' => 'EMP-001',
                'phone' => '+1234567890',
                'facility_scope' => json_encode([1, 2, 3]),
                'access_level' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            echo "Admin user created.\n";
        }

        if (User::where('email', 'manager@omegacmms.com')->exists()) {
            echo "Manager user already exists. Skipping...\n";
        } else {
            // Create manager user
            User::create([
                'name' => 'Facility Manager',
                'email' => 'manager@omegacmms.com',
                'password' => Hash::make('Manager@123'),
                'role_id' => 2,
                'employee_id' => 'EMP-002',
                'phone' => '+1234567891',
                'facility_scope' => json_encode([1]),
                'access_level' => 'edit',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            echo "Manager user created.\n";
        }

        if (User::where('email', 'tech@omegacmms.com')->exists()) {
            echo "Technician user already exists. Skipping...\n";
        } else {
            // Create technician user
            User::create([
                'name' => 'Field Technician',
                'email' => 'tech@omegacmms.com',
                'password' => Hash::make('Tech@123'),
                'role_id' => 3,
                'employee_id' => 'EMP-003',
                'phone' => '+1234567892',
                'facility_scope' => json_encode([1]),
                'access_level' => 'view',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            echo "Technician user created.\n";
        }

        if (User::where('email', 'viewer@omegacmms.com')->exists()) {
            echo "Viewer user already exists. Skipping...\n";
        } else {
            // Create viewer user
            User::create([
                'name' => 'Report Viewer',
                'email' => 'viewer@omegacmms.com',
                'password' => Hash::make('Viewer@123'),
                'role_id' => 4,
                'employee_id' => 'EMP-004',
                'phone' => '+1234567893',
                'facility_scope' => json_encode([1]),
                'access_level' => 'view',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            echo "Viewer user created.\n";
        }
    }
}