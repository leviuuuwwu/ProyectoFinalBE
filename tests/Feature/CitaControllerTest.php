<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CitaControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @return User */
    private function createPaciente(array $attributes = [])
    {
        $user = new User(array_merge([
            'nombre' => 'Paciente',
            'apellido' => 'Prueba',
            'email' => 'paciente_' . uniqid() . '@test.com',
            'telefono' => '77777777',
            'rol' => 'paciente',
            'password' => 'password',
        ], $attributes));

        $user->save();

        return $user;
    }

    /** @return User */
    private function createMedico(array $attributes = [])
    {
        $user = new User(array_merge([
            'nombre' => 'Medico',
            'apellido' => 'Prueba',
            'email' => 'medico_' . uniqid() . '@test.com',
            'telefono' => '88888888',
            'rol' => 'medico',
            'password' => 'password',
        ], $attributes));

        $user->save();

        return $user;
    }

    public function test_usuario_no_autenticado_no_puede_ver_citas()
    {
        $response = $this->getJson('/api/citas');
        $response->assertStatus(401);
    }

    public function test_usuario_no_autenticado_no_puede_ver_historial_de_paciente()
    {
        /** @var User $paciente */
        $paciente = $this->createPaciente();

        $response = $this->getJson('/api/pacientes/' . $paciente->id . '/historial');

        $response->assertStatus(401);
    }

    public function test_paciente_solo_puede_ver_sus_citas()
    {
        /** @var User $paciente */
        $paciente = $this->createPaciente();
        /** @var User $otroPaciente */
        $otroPaciente = $this->createPaciente();
        /** @var User $medico */
        $medico = $this->createMedico();

        Cita::factory()->create(['paciente_id' => $paciente->id, 'medico_id' => $medico->id]);
        Cita::factory()->create(['paciente_id' => $otroPaciente->id, 'medico_id' => $medico->id]);

        $this->actingAs($paciente, 'sanctum');
        $response = $this->getJson('/api/citas');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_paciente_solo_puede_ver_su_historial_pasado_y_completado()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 7, 10, 0, 0));

        try {
            /** @var User $paciente */
            $paciente = $this->createPaciente();
            /** @var User $otroPaciente */
            $otroPaciente = $this->createPaciente();
            /** @var User $medico */
            $medico = $this->createMedico();

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->subDays(4),
                'estado' => 'Atendida',
                'motivo' => 'control general 1',
            ]);

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->subDay(),
                'estado' => 'Atendida',
                'motivo' => 'control general 2',
            ]);

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->addDay(),
                'estado' => 'Atendida',
                'motivo' => 'no debe salir por ser futura',
            ]);

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->subDays(2),
                'estado' => 'Cancelada',
                'motivo' => 'no debe salir por cancelada',
            ]);

            Cita::factory()->create([
                'paciente_id' => $otroPaciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => now()->subDays(3),
                'estado' => 'Atendida',
                'motivo' => 'no debe salir por otro paciente',
            ]);

            $this->actingAs($paciente, 'sanctum');
            $response = $this->getJson('/api/pacientes/' . $paciente->id . '/historial');

            $response->assertStatus(200)
                ->assertJsonCount(2, 'data')
                ->assertJsonPath('data.0.motivo', 'control general 2')
                ->assertJsonPath('data.1.motivo', 'control general 1')
                ->assertJsonMissing(['motivo' => 'no debe salir por ser futura'])
                ->assertJsonMissing(['motivo' => 'no debe salir por cancelada'])
                ->assertJsonMissing(['motivo' => 'no debe salir por otro paciente']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_otro_paciente_no_puede_ver_historial_ajeno()
    {
        /** @var User $paciente */
        $paciente = $this->createPaciente();
        /** @var User $otroPaciente */
        $otroPaciente = $this->createPaciente();

        $this->actingAs($otroPaciente, 'sanctum');
        $response = $this->getJson('/api/pacientes/' . $paciente->id . '/historial');

        $response->assertStatus(403);
    }

    public function test_error_de_validacion_al_crear_cita()
    {
        /** @var User $paciente */
        $paciente = $this->createPaciente();

        $this->actingAs($paciente, 'sanctum');
        $response = $this->postJson('/api/citas', [
            'fecha_hora' => 'fecha-invalida'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['medico_id', 'fecha_hora']);
    }

    public function test_creacion_de_cita_exitosa()
    {
        /** @var User $paciente */
        $paciente = $this->createPaciente();
        /** @var User $medico */
        $medico = $this->createMedico();

        $especialidad = Especialidad::query()->create([
            'nombre' => 'Medicina General '.uniqid(),
        ]);

        $servicio = Servicio::query()->create([
            'especialidad_id' => $especialidad->id,
            'nombre' => 'Consulta General',
            'duracion_minutos' => 30,
            'activo' => true,
        ]);

        $datos = [
            'medico_id' => $medico->id,
            'servicio_id' => $servicio->id,
            'fecha_hora' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'motivo' => 'chequeo general'
        ];

        $this->actingAs($paciente, 'sanctum');
        $response = $this->postJson('/api/citas', $datos);

        $response->assertStatus(201)
            ->assertJsonPath('data.medico_id', $medico->id);

        $this->assertDatabaseHas('citas', [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'servicio_id' => $servicio->id,
        ]);
    }

    public function test_medico_asignado_puede_agregar_notas_a_cita_completada()
    {
        /** @var User $paciente */
        $paciente = $this->createPaciente();
        /** @var User $medico */
        $medico = $this->createMedico();

        $cita = Cita::factory()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'estado' => 'Atendida',
            'notas' => null,
        ]);

        $this->actingAs($medico, 'sanctum');

        $response = $this->postJson('/api/citas/' . $cita->uuid . '/notas', [
            'notas' => 'paciente estable, continuar con control en 30 dias.',
            'receta' => 'paracetamol 500mg cada 8 horas por 5 dias',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.notas', "paciente estable, continuar con control en 30 dias.\n\nReceta: paracetamol 500mg cada 8 horas por 5 dias");

        $this->assertDatabaseHas('citas', [
            'uuid' => $cita->uuid,
            'notas' => "paciente estable, continuar con control en 30 dias.\n\nReceta: paracetamol 500mg cada 8 horas por 5 dias",
        ]);
    }

    public function test_medico_no_asignado_no_puede_agregar_notas_a_cita_ajena()
    {
        /** @var User $paciente */
        $paciente = $this->createPaciente();
        /** @var User $medicoAsignado */
        $medicoAsignado = $this->createMedico();
        /** @var User $otroMedico */
        $otroMedico = $this->createMedico();

        $cita = Cita::factory()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medicoAsignado->id,
            'estado' => 'Atendida',
        ]);

        $this->actingAs($otroMedico, 'sanctum');

        $response = $this->postJson('/api/citas/' . $cita->uuid . '/notas', [
            'notas' => 'intento de acceso no permitido',
        ]);

        $response->assertStatus(403);
    }

    public function test_no_se_pueden_agregar_notas_si_la_cita_no_esta_completada()
    {
        /** @var User $paciente */
        $paciente = $this->createPaciente();
        /** @var User $medico */
        $medico = $this->createMedico();

        $cita = Cita::factory()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'estado' => 'Programada',
        ]);

        $this->actingAs($medico, 'sanctum');

        $response = $this->postJson('/api/citas/' . $cita->uuid . '/notas', [
            'notas' => 'esto no deberia guardarse',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'solo se pueden agregar notas a una cita completada.');
    }

    public function test_validacion_falla_si_no_envia_notas_ni_receta()
    {
        /** @var User $paciente */
        $paciente = $this->createPaciente();
        /** @var User $medico */
        $medico = $this->createMedico();

        $cita = Cita::factory()->create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'estado' => 'Atendida',
        ]);

        $this->actingAs($medico, 'sanctum');

        $response = $this->postJson('/api/citas/' . $cita->uuid . '/notas', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['notas', 'receta']);
    }
}
