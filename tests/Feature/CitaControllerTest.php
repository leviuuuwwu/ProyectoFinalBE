<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_no_autenticado_no_puede_ver_citas()
    {
        $response = $this->getJson('/api/citas');
        $response->assertStatus(401);
    }

    public function test_paciente_solo_puede_ver_sus_citas()
    {
        $paciente = User::factory()->create(['rol' => 'paciente']);
        $otroPaciente = User::factory()->create(['rol' => 'paciente']);
        $medico = User::factory()->create(['rol' => 'medico']);

        Cita::factory()->create(['paciente_id' => $paciente->id, 'medico_id' => $medico->id]);
        Cita::factory()->create(['paciente_id' => $otroPaciente->id, 'medico_id' => $medico->id]);

        $response = $this->actingAs($paciente)->getJson('/api/citas');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_error_de_validacion_al_crear_cita()
    {
        $paciente = User::factory()->create(['rol' => 'paciente']);

        $response = $this->actingAs($paciente)->postJson('/api/citas', [
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

        $response = $this->actingAs($paciente)->postJson('/api/citas', $datos);

        $response->assertStatus(201)
            ->assertJsonPath('data.medico_id', $medico->id);

        $this->assertDatabaseHas('citas', [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
        ]);
    }
}
