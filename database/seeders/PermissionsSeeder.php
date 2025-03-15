<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Departments
            'create departments',
            'edit departments',
            'delete departments',
            'view departments',

            // Documents
            'create documents',
            'edit documents',
            'delete documents',
            'view documents',

            // Users
            'create users',
            'edit users',
            'delete users',
            'view users',

            // Employees
            'create employees',
            'edit employees',
            'delete employees',
            'view employees',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        }
    }
}
