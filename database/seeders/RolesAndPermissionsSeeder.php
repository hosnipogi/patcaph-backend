<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create(['name' => 'admin']);
        $member = Role::create(['name' => 'member']);
        $fba = Role::create(['name' => 'fba']);
        $treasurer = Role::create(['name' => 'treasurer']);

        $updateContribution = Permission::create(['name' => 'update contribution']);
        $viewMembers = Permission::create(['name' => 'view members']);
        $updateMemberRole = Permission::create(['name' => 'update member role']);
        $updateFbaStatus = Permission::create(['name' => 'update fba status']);

        $treasurerPermissions = [$viewMembers, $updateContribution];
        $treasurer->syncPermissions($treasurerPermissions);

        $fbaPermissions = [$updateFbaStatus];
        $fba->syncPermissions($fbaPermissions);
    }
}
