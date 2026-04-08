<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'rol' => ['nullable', 'string', Rule::in(Roles::all())],
        ]);

        $query = User::query()->with('roles')->orderBy('apellido')->orderBy('nombre');

        if ($request->filled('rol')) {
            $query->role($request->string('rol'));
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'tipo' => ['required', 'string', Rule::in(Roles::creablesPorAdmin())],
        ]);

        $usuario = DB::transaction(function () use ($request) {
            $user = User::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'password' => Hash::make($request->password),
                'activo' => true,
            ]);

            $user->assignRole($request->tipo);

            return $user->load('roles');
        });

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'data' => $usuario,
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'telefono' => 'nullable|string|max:20',
            'activo' => 'sometimes|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($request, $user) {
            foreach (['nombre', 'apellido', 'email', 'telefono'] as $campo) {
                if ($request->has($campo)) {
                    $user->{$campo} = $request->input($campo);
                }
            }
            if ($request->has('activo')) {
                $user->activo = $request->boolean('activo');
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
        });

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'data' => $user->fresh()->load('roles'),
        ]);
    }

    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->delete();
        });

        return response()->json([
            'message' => 'Usuario dado de baja exitosamente',
        ]);
    }

    /**
     * Sincroniza roles del usuario (reemplaza el conjunto actual por el enviado).
     */
    public function syncRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', Rule::in(Roles::all())],
        ]);

        DB::transaction(function () use ($user, $request) {
            $user->syncRoles($request->roles);
        });

        return response()->json([
            'message' => 'Roles actualizados exitosamente',
            'data' => $user->fresh()->load('roles'),
        ]);
    }
}
