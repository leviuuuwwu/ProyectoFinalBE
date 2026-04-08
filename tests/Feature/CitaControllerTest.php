<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CitaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_no_autenticado_no_puede_ver_citas()
    {
        $response = $this->getJson('/api/citas');
        $response->assertStatus(401);
    }

    public function test_usuario_no_autenticado_no_puede_ver_historial_de_paciente()
    {
        $paciente = User::factory()->create(['rol' => 'paciente']);

        $response = $this->getJson('/api/pacientes/' . $paciente->id . '/historial');

        $response->assertStatus(401);
    }

    public function test_paciente_solo_puede_ver_sus_citas()
    {
        $paciente = User::factory()->create(['rol' => 'paciente']);
        $otroPaciente = User::factory()->create(['rol' => 'paciente']);
        $medico = User::factory()->create(['rol' => 'medico']);

        Cita::factory()->create(['paciente_id' => $paciente->id, 'medico_id' => $medico->id]);
        Cita::factory()->create(['paciente_id' => $otroPaciente->id, 'medico_id' => $medico->id]);

        Sanctum::actingAs($paciente);
        $response = $this->getJson('/api/citas');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_paciente_solo_puede_ver_su_historial_pasado_y_completado()
    {
        Carbon::setTestNow(Carbon::parse('2026-04-07 10:00:00'));
        try {
            $paciente = User::factory()->create(['rol' => 'paciente']);
            $otroPaciente = User::factory()->create(['rol' => 'paciente']);
            $medico = User::factory()->create(['rol' => 'medico']);

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->subDays(4),
                'estado' => 'Atendida',
                'motivo' => 'Control general 1',
            ]);

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->subDay(),
                'estado' => 'Atendida',
                'motivo' => 'Control general 2',
            ]);

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->addDay(),
                'estado' => 'Atendida',
                'motivo' => 'No debe salir por ser futura',
            ]);

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->subDays(2),
                'estado' => 'Cancelada',
                'motivo' => 'No debe salir por cancelada',
            ]);

            Cita::factory()->create([
                'paciente_id' => $otroPaciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->subDays(3),
                'estado' => 'Atendida',
                'motivo' => 'No debe salir por otro paciente',
            ]);

            Sanctum::actingAs($paciente);
            $response = $this->getJson('/api/pacientes/' . $paciente->id . '/historial');

            $response->assertStatus(200)
                ->assertJsonCount(2, 'data')
                ->assertJsonPath('data.0.motivo', 'Control general 2')
                ->assertJsonPath('data.1.motivo', 'Control general 1')
                ->assertJsonMissing(['motivo' => 'No debe salir por ser futura'])
                ->assertJsonMissing(['motivo' => 'No debe salir por cancelada'])
                ->assertJsonMissing(['motivo' => 'No debe salir por otro paciente']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_otro_paciente_no_puede_ver_historial_ajeno()
    {
        $paciente = User::factory()->create(['rol' => 'paciente']);
        $otroPaciente = User::factory()->create(['rol' => 'paciente']);

        Sanctum::actingAs($otroPaciente);
        $response = $this->getJson('/api/pacientes/' . $paciente->id . '/historial');

        $response->assertStatus(403);
    }

    public function test_error_de_validacion_al_crear_cita()
    {
        $paciente = User::factory()->create(['rol' => 'paciente']);

        Sanctum::actingAs($paciente);
        $response = $this->postJson('/api/citas', [
            'fecha_hora' => 'fecha-invalida'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['medico_id', 'fecha_hora']);
    }

    public function test_creacion_de_cita_exitosa()
    {
        $paciente = User::factory()->create(['rol' => 'paciente']);
        $medico = User::factory()->create(['rol' => 'medico']);

        $datos = [
            'medico_id' => $medico->id,
            'fecha_hora' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'motivo' => 'Chequeo general'
        ];

        Sanctum::actingAs($paciente);
        $response = $this->postJson('/api/citas', $datos);

        $response->assertStatus(201)
            ->assertJsonPath('data.medico_id', $medico->id);

        $this->assertDatabaseHas('citas', [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
        ]);
    }
}
