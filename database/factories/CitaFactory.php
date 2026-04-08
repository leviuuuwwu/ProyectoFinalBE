<?php

namespace Database\Factories;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Cita>
 */
class CitaFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'uuid' => (string) Str::uuid(),
			'paciente_id' => User::factory()->paciente(),
			'medico_id' => User::factory()->medico(),
			'fecha_hora' => now()->addDays(2),
			'motivo' => fake()->sentence(4),
			'estado' => 'Programada',
			'notas' => null,
		];
	}
}

