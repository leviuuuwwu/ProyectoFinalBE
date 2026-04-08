<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
	use RefreshDatabase;

	private function createUser($rol, $emailPrefix)
	{
		$user = new User([
			'nombre' => ucfirst($rol),
			'apellido' => 'Prueba',
			'email' => $emailPrefix . '_' . uniqid() . '@test.com',
			'telefono' => '77777777',
			'rol' => $rol,
			'password' => 'password',
		]);

		$user->save();

		return $user;
	}

	public function test_usuario_no_autenticado_no_puede_ver_dashboard()
	{
		$response = $this->getJson('/api/admin/dashboard');

		$response->assertStatus(401);
	}

	public function test_usuario_que_no_es_admin_no_puede_ver_dashboard()
	{
		$paciente = $this->createUser('paciente', 'paciente');

		$this->actingAs($paciente, 'sanctum');
		$response = $this->getJson('/api/admin/dashboard');

		$response->assertStatus(403);
	}

	public function test_admin_puede_ver_dashboard_con_estadisticas()
	{
		Carbon::setTestNow(Carbon::create(2026, 4, 7, 10, 0, 0));

		try {
			$admin = $this->createUser('admin', 'admin');
			$paciente = $this->createUser('paciente', 'paciente');
			$medico = $this->createUser('medico', 'medico');

			$especialidad = Especialidad::create([
				'nombre' => 'odontologia',
				'descripcion' => 'prueba',
				'activo' => true,
			]);

			Servicio::create([
				'especialidad_id' => $especialidad->id,
				'nombre' => 'limpieza',
				'descripcion' => 'servicio de prueba',
				'duracion_minutos' => 30,
				'precio' => 20,
				'activo' => true,
			]);

			Cita::factory()->create([
				'paciente_id' => $paciente->id,
				'medico_id' => $medico->id,
				'fecha_hora' => now(),
				'estado' => 'Programada',
			]);

			Cita::factory()->create([
				'paciente_id' => $paciente->id,
				'medico_id' => $medico->id,
				'fecha_hora' => now()->subDay(),
				'estado' => 'Atendida',
			]);

			$this->actingAs($admin, 'sanctum');
			$response = $this->getJson('/api/admin/dashboard');

			$response->assertStatus(200)
				->assertJsonPath('data.citas_hoy', 1)
				->assertJsonPath('data.citas_totales', 2)
				->assertJsonPath('data.citas_atendidas', 1)
				->assertJsonPath('data.citas_programadas', 1)
				->assertJsonPath('data.especialidades_activas', 1)
				->assertJsonPath('data.servicios_activos', 1)
				->assertJsonPath('data.ingresos', 0);
		} finally {
			Carbon::setTestNow();
		}
	}
}


