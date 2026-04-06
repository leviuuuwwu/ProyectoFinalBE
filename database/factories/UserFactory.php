<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->numerify('##########'),
            'password' => static::$password ??= Hash::make('password'),
            'rol' => 'paciente',
            'remember_token' => Str::random(10),
        ];
    }

    public function medico(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'medico',
        ]);
    }

    public function paciente(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'paciente',
        ]);
    }
}
