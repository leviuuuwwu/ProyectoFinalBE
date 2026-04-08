<?php

namespace Database\Seeders;

use App\Support\Roles;
use Illuminate\Database\Seeder;
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
    }
}
