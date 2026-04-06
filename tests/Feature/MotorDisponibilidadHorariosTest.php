<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\ProfesionalBloqueo;
use App\Models\ProfesionalHorario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MotorDisponibilidadHorariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_api_profesionales_horarios_medico_define_horario_base_lunes_a_viernes(): void
    {
        $medico = User::factory()->medico()->create();

        $dias = [];
        for ($d = 1; $d <= 5; $d++) {
            $dias[] = ['dia_semana' => $d, 'hora_inicio' => '08:00', 'hora_fin' => '16:00'];
        }

        $response = $this->actingAs($medico, 'sanctum')->postJson('/api/profesionales/horarios', [
            'intervalo_minutos' => 30,
            'dias' => $dias,
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Horario base guardado.');

        $this->assertDatabaseCount('profesional_horarios', 5);
        $this->assertSame(30, (int) ProfesionalHorario::where('user_id', $medico->id)->value('intervalo_minutos'));
    }

    public function test_get_api_profesionales_uuid_horarios_ve_horario_del_medico(): void
    {
        $medico = User::factory()->medico()->create();

        ProfesionalHorario::create([
            'user_id' => $medico->id,
            'dia_semana' => 2,
            'hora_inicio' => '09:00',
            'hora_fin' => '13:00',
            'intervalo_minutos' => 30,
        ]);

        $response = $this->getJson("/api/profesionales/{$medico->id}/horarios");

        $response->assertOk()
            ->assertJsonStructure(['data']);

        $this->assertCount(1, $response->json('data'));
        $this->assertSame((string) $medico->id, $response->json('data.0.user_id'));
    }

    public function test_get_api_profesionales_uuid_horarios_404_si_uuid_no_es_medico(): void
    {
        $paciente = User::factory()->paciente()->create();

        $this->getJson("/api/profesionales/{$paciente->id}/horarios")->assertNotFound();
    }

    public function test_post_api_profesionales_bloqueos_medico_bloquea_dia(): void
    {
        $medico = User::factory()->medico()->create();

        $response = $this->actingAs($medico, 'sanctum')->postJson('/api/profesionales/bloqueos', [
            'fecha_inicio' => '2026-07-10',
            'fecha_fin' => null,
            'motivo' => 'Enfermedad',
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Bloqueo registrado.');

        $this->assertDatabaseHas('profesional_bloqueos', [
            'user_id' => $medico->id,
            'motivo' => 'Enfermedad',
        ]);
    }

    public function test_get_api_profesionales_uuid_disponibilidad_endpoint_estrella_huecos_libres(): void
    {
        $medico = User::factory()->medico()->create();

        ProfesionalHorario::create([
            'user_id' => $medico->id,
            'dia_semana' => 3,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
            'intervalo_minutos' => 30,
        ]);

        $response = $this->getJson("/api/profesionales/{$medico->id}/disponibilidad?fecha=2026-04-15");

        $response->assertOk()
            ->assertJsonPath('fecha', '2026-04-15');

        $this->assertSame(
            ['2026-04-15 08:00:00', '2026-04-15 08:30:00', '2026-04-15 09:00:00', '2026-04-15 09:30:00'],
            $response->json('data')
        );
    }

    public function test_disponibilidad_resta_citas_ocupadas(): void
    {
        $medico = User::factory()->medico()->create();
        $paciente = User::factory()->paciente()->create();

        ProfesionalHorario::create([
            'user_id' => $medico->id,
            'dia_semana' => 3,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
            'intervalo_minutos' => 30,
        ]);

        Cita::create([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'servicio_id' => null,
            'fecha_hora' => '2026-04-15 09:00:00',
            'duracion_minutos' => 30,
            'estado' => 'Programada',
        ]);

        $response = $this->getJson("/api/profesionales/{$medico->id}/disponibilidad?fecha=2026-04-15");

        $response->assertOk();
        $this->assertSame(
            ['2026-04-15 08:00:00', '2026-04-15 08:30:00', '2026-04-15 09:30:00'],
            $response->json('data')
        );
    }

    public function test_disponibilidad_resta_bloqueos_dia_completo(): void
    {
        $medico = User::factory()->medico()->create();

        ProfesionalHorario::create([
            'user_id' => $medico->id,
            'dia_semana' => 3,
            'hora_inicio' => '08:00',
            'hora_fin' => '16:00',
            'intervalo_minutos' => 30,
        ]);

        ProfesionalBloqueo::create([
            'user_id' => $medico->id,
            'fecha_inicio' => '2026-04-15',
            'fecha_fin' => null,
            'motivo' => 'Vacaciones',
        ]);

        $this->getJson("/api/profesionales/{$medico->id}/disponibilidad?fecha=2026-04-15")
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_disponibilidad_bloqueo_por_rango_cubre_fecha_intermedia(): void
    {
        $medico = User::factory()->medico()->create();

        ProfesionalHorario::create([
            'user_id' => $medico->id,
            'dia_semana' => 4,
            'hora_inicio' => '08:00',
            'hora_fin' => '10:00',
            'intervalo_minutos' => 30,
        ]);

        ProfesionalBloqueo::create([
            'user_id' => $medico->id,
            'fecha_inicio' => '2026-04-13',
            'fecha_fin' => '2026-04-20',
            'motivo' => 'Vacaciones',
        ]);

        $this->getJson("/api/profesionales/{$medico->id}/disponibilidad?fecha=2026-04-16")
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_disponibilidad_cita_cancelada_no_ocupa_hueco(): void
    {
        $medico = User::factory()->medico()->create();
        $paciente = User::factory()->paciente()->create();

        ProfesionalHorario::create([
            'user_id' => $medico->id,
            'dia_semana' => 3,
            'hora_inicio' => '08:00',
            'hora_fin' => '09:00',
            'intervalo_minutos' => 30,
        ]);

        Cita::create([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'fecha_hora' => '2026-04-15 08:00:00',
            'duracion_minutos' => 30,
            'estado' => 'Cancelada',
        ]);

        $response = $this->getJson("/api/profesionales/{$medico->id}/disponibilidad?fecha=2026-04-15");

        $this->assertSame(['2026-04-15 08:00:00', '2026-04-15 08:30:00'], $response->json('data'));
    }

    public function test_disponibilidad_intervalo_60_minutos(): void
    {
        $medico = User::factory()->medico()->create();

        ProfesionalHorario::create([
            'user_id' => $medico->id,
            'dia_semana' => 3,
            'hora_inicio' => '08:00',
            'hora_fin' => '11:00',
            'intervalo_minutos' => 60,
        ]);

        $response = $this->getJson("/api/profesionales/{$medico->id}/disponibilidad?fecha=2026-04-15");

        $this->assertSame(
            ['2026-04-15 08:00:00', '2026-04-15 09:00:00', '2026-04-15 10:00:00'],
            $response->json('data')
        );
    }

    public function test_post_horarios_reemplaza_plantilla_anterior(): void
    {
        $medico = User::factory()->medico()->create();

        ProfesionalHorario::create([
            'user_id' => $medico->id,
            'dia_semana' => 1,
            'hora_inicio' => '07:00',
            'hora_fin' => '08:00',
            'intervalo_minutos' => 30,
        ]);

        $this->actingAs($medico, 'sanctum')->postJson('/api/profesionales/horarios', [
            'intervalo_minutos' => 30,
            'dias' => [
                ['dia_semana' => 1, 'hora_inicio' => '10:00', 'hora_fin' => '11:00'],
            ],
        ])->assertCreated();

        $this->assertDatabaseCount('profesional_horarios', 1);
        $this->assertDatabaseHas('profesional_horarios', [
            'user_id' => $medico->id,
            'hora_inicio' => '10:00',
            'hora_fin' => '11:00',
        ]);
    }

    public function test_disponibilidad_requiere_query_fecha(): void
    {
        $medico = User::factory()->medico()->create();

        $this->getJson("/api/profesionales/{$medico->id}/disponibilidad")->assertStatus(422);
    }

    public function test_post_horarios_sin_token_401(): void
    {
        $this->postJson('/api/profesionales/horarios', [
            'intervalo_minutos' => 30,
            'dias' => [['dia_semana' => 1, 'hora_inicio' => '08:00', 'hora_fin' => '16:00']],
        ])->assertUnauthorized();
    }

    public function test_post_bloqueos_sin_token_401(): void
    {
        $this->postJson('/api/profesionales/bloqueos', ['fecha_inicio' => '2026-01-01'])->assertUnauthorized();
    }

    public function test_post_horarios_paciente_403(): void
    {
        $paciente = User::factory()->paciente()->create();

        $this->actingAs($paciente, 'sanctum')->postJson('/api/profesionales/horarios', [
            'intervalo_minutos' => 30,
            'dias' => [['dia_semana' => 1, 'hora_inicio' => '08:00', 'hora_fin' => '16:00']],
        ])->assertForbidden();
    }

    public function test_post_bloqueos_paciente_403(): void
    {
        $paciente = User::factory()->paciente()->create();

        $this->actingAs($paciente, 'sanctum')->postJson('/api/profesionales/bloqueos', [
            'fecha_inicio' => '2026-08-01',
        ])->assertForbidden();
    }
}
