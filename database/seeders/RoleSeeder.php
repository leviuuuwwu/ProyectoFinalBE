<?php

namespace Database\Seeders;

use App\Support\Roles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Roles::all() as $nombre) {
            Role::firstOrCreate(
                ['name' => $nombre, 'guard_name' => 'web']
            );
        }

        $permisoCitas = Permission::firstOrCreate([
            'name' => 'gestionar citas',
            'guard_name' => 'web',
        ]);

        $permisoUsuarios = Permission::firstOrCreate([
            'name' => 'gestionar usuarios',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => Roles::ADMIN,
            'guard_name' => 'web',
        ]);

        $admin->givePermissionTo([
            $permisoCitas,
            $permisoUsuarios,
        ]);
    }
}
