<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الأدوار
        $roles = [
            'super-admin' => Permission::all()->pluck('name')->toArray(), // كل الصلاحيات
            'admin' => [
                'create departments', 'edit departments', 'delete departments', 'view departments',
                'create documents', 'edit documents', 'delete documents', 'view documents',
                'create users', 'edit users', 'delete users', 'view users',
                'create employees', 'edit employees', 'delete employees', 'view employees',
            ],
            'editor' => [
                'edit documents', 'view documents',
                'edit employees', 'view employees',
            ],
            'viewer' => [
                'view departments', 'view documents', 'view users', 'view employees',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'api']);
            $role->syncPermissions($permissions);
        }
    }
}
