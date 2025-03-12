<?php

namespace Database\Seeders;

use App\Enum\PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create permissions
        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate(['name' => $permission->value]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to admin
        $adminRole->syncPermissions(PermissionEnum::values());

        // Assign specific permissions to user
        $userRole->syncPermissions([
            PermissionEnum::EVENT_LIST->value,
            PermissionEnum::EVENT_VIEW->value,
            PermissionEnum::EVENT_JOIN->value,

            PermissionEnum::USER_LIST->value,
            PermissionEnum::USER_VIEW->value,
            PermissionEnum::USER_CREATE->value,
            PermissionEnum::USER_EDIT->value,
            PermissionEnum::USER_DELETE->value,
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
