<?php

use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (Roles::all() as $nombre) {
            Role::firstOrCreate(
                ['name' => $nombre, 'guard_name' => 'web']
            );
        }

        User::query()->whereDoesntHave('roles')->each(function (User $user): void {
            $user->assignRole(Roles::PACIENTE);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
