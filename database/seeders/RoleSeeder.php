<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Check and create roles if they don't exist
        $roles = [
            [
                'name' => 'Administrator',
                'code' => 'admin',
                'description' => 'Full system access',
                'permissions' => json_encode(['*']),
                'is_active' => true,
            ],
            [
                'name' => 'Manager',
                'code' => 'manager',
                'description' => 'Facility management access',
                'permissions' => json_encode([
                    'facility:view', 'facility:edit',
                    'asset:view', 'asset:edit',
                    'work-order:view', 'work-order:edit', 'work-order:approve',
                    'inventory:view', 'inventory:edit',
                    'report:view', 'report:generate'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Technician',
                'code' => 'technician',
                'description' => 'Work order execution access',
                'permissions' => json_encode([
                    'work-order:view', 'work-order:edit',
                    'asset:view',
                    'inventory:view'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Viewer',
                'code' => 'viewer',
                'description' => 'Read-only access',
                'permissions' => json_encode([
                    'facility:view',
                    'asset:view',
                    'work-order:view',
                    'inventory:view',
                    'report:view'
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            if (!Role::where('code', $roleData['code'])->exists()) {
                Role::create($roleData);
                echo "Role {$roleData['name']} created.\n";
            } else {
                echo "Role {$roleData['name']} already exists. Skipping...\n";
            }
        }
    }
}