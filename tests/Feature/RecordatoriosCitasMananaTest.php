<?php

namespace Tests\Feature;

use App\Jobs\EnviarRecordatoriosCitasManana;
use App\Models\Cita;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RecordatoriosCitasMananaTest extends TestCase
{
    use RefreshDatabase;

    private function createUser($rol)
    {
        $user = new User([
            'nombre' => ucfirst($rol),
            'apellido' => 'Prueba',
            'email' => $rol . '_' . uniqid() . '@test.com',
            'telefono' => '77777777',
            'rol' => $rol,
            'password' => 'password',
        ]);

        $user->save();

        return $user;
    }

    public function test_el_job_busca_solo_las_citas_de_manana()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 7, 9, 0, 0));
        Log::spy();

        try {
            $paciente = $this->createUser('paciente');
            $medico = $this->createUser('medico');

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => Carbon::tomorrow()->setHour(10),
                'estado' => 'Programada',
            ]);

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => Carbon::today()->setHour(10),
                'estado' => 'Programada',
            ]);

            Cita::factory()->create([
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
                'fecha_hora' => Carbon::tomorrow()->setHour(11),
                'estado' => 'Cancelada',
            ]);

            app(EnviarRecordatoriosCitasManana::class)->handle();

            Log::shouldHaveReceived('info')->with('recordatorio de cita enviado', \Mockery::on(function ($context) {
                return isset($context['cita_uuid']);
            }))->once();

            Log::shouldHaveReceived('info')->with('recordatorios revisados', [
                'cantidad' => 1,
            ])->once();
        } finally {
            Carbon::setTestNow();
        }
    }
}

