<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = collect([
            'view dashboard',
            'manage products',
            'manage orders',
            'manage customers',
            'manage coupons',
            'manage reviews',
            'manage settings',
            'view payment logs',
            'export data',
        ])->map(fn (string $name) => Permission::firstOrCreate(['name' => $name]));

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        $admin->syncPermissions($permissions);
        $staff->syncPermissions($permissions->whereNotIn('name', ['manage settings']));

        $user = User::updateOrCreate(
            ['email' => 'admin@maisondemystere.ae'],
            [
                'name' => 'Maison De Mystere Admin',
                'phone' => '+971501234567',
                'password' => Hash::make('password'),
                'preferred_locale' => 'en',
                'is_admin' => true,
            ],
        );

        $user->assignRole($admin);

        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Sample Customer',
                'phone' => '+971501111111',
                'password' => Hash::make('password'),
                'preferred_locale' => 'en',
            ],
        )->assignRole($customer);
    }
}
