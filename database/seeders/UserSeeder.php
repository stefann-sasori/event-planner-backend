<?php

namespace Database\Seeders;

use App\Enum\RoleEnum;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find existing roles
        $adminRole = Role::where('name', RoleEnum::ADMIN->value)->first();
        $userRole = Role::where('name', RoleEnum::USER->value)->first();

        if (!$adminRole || !$userRole) {
            $this->command->error("Roles not found. Please run RoleAndPermissionSeeder first.");
            return;
        }

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'), // Change this password in production
            ]
        );
        $admin->assignRole($adminRole);

        // Create Regular User
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'), // Change this password in production
            ]
        );
        $user->assignRole($userRole);

        $this->command->info('Admin and Regular User created successfully.');
    }
}
