<?php

namespace Database\Seeders;

use App\Support\Roles;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\ProfesionalHorario;
use App\Models\Servicio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $odontologia = Especialidad::firstOrCreate(
            ['nombre' => 'Odontología'],
            [
                'descripcion' => 'Especialidad encargada de la salud bucodental.',
                'activo' => true,
            ]
        );

        Servicio::firstOrCreate(
            [
                'especialidad_id' => $odontologia->id,
                'nombre' => 'Limpieza Dental Básica',
            ],
            [
                'descripcion' => 'Limpieza con ultrasonido y pulido profiláctico.',
                'duracion_minutos' => 30,
                'precio' => 25.00,
            ]
        );

        Servicio::firstOrCreate(
            [
                'especialidad_id' => $odontologia->id,
                'nombre' => 'Extracción de Cordal',
            ],
            [
                'descripcion' => 'Procedimiento quirúrgico para la remoción de muelas del juicio.',
                'duracion_minutos' => 60,
                'precio' => 75.00,
            ]
        );

        $psicologia = Especialidad::firstOrCreate(
            ['nombre' => 'Psicología'],
            [
                'descripcion' => 'Atención profesional para la salud mental y emocional.',
                'activo' => true,
            ]
        );

        Servicio::firstOrCreate(
            [
                'especialidad_id' => $psicologia->id,
                'nombre' => 'Terapia Individual',
            ],
            [
                'descripcion' => 'Sesión presencial o virtual de 45 minutos.',
                'duracion_minutos' => 45,
                'precio' => 30.00,
            ]
        );

        $medicina = Especialidad::firstOrCreate(
            ['nombre' => 'Medicina General'],
            [
                'descripcion' => 'Atención primaria, evaluación y diagnóstico preventivo.',
                'activo' => true,
            ]
        );

        $consultaGeneral = Servicio::firstOrCreate(
            [
                'especialidad_id' => $medicina->id,
                'nombre' => 'Consulta General',
            ],
            [
                'descripcion' => 'Evaluación de síntomas, toma de presión y receta médica.',
                'duracion_minutos' => 30,
                'precio' => 20.00,
            ]
        );

        $medico = User::firstOrCreate(
            ['email' => 'medico@example.com'],
            [
                'nombre' => 'Ana',
                'apellido' => 'Médica',
                'telefono' => '5551111111',
                'password' => Hash::make('password'),
                'rol' => 'medico',
            ]
        );
        $medico->assignRole(Roles::PROFESIONAL);

        $paciente = User::firstOrCreate(
            ['email' => 'paciente@example.com'],
            [
                'nombre' => 'Luis',
                'apellido' => 'Paciente',
                'telefono' => '5552222222',
                'password' => Hash::make('password'),
                'rol' => 'paciente',
            ]
        );
        $paciente->assignRole(Roles::PACIENTE);

        for ($d = 1; $d <= 5; $d++) {
            ProfesionalHorario::firstOrCreate(
                [
                    'user_id' => $medico->id,
                    'dia_semana' => $d,
                ],
                [
                    'hora_inicio' => '08:00',
                    'hora_fin' => '16:00',
                    'intervalo_minutos' => 30,
                ]
            );
        }

        $demo = Carbon::parse('2026-04-15 09:00:00', config('app.timezone'));

        Cita::firstOrCreate(
            [
                'medico_id' => $medico->id,
                'paciente_id' => $paciente->id,
                'fecha_hora' => $demo,
            ],
            [
                'servicio_id' => $consultaGeneral->id,
                'duracion_minutos' => 30,
                'estado' => 'Programada',
                'motivo' => 'Demo disponibilidad',
            ]
        );
    }
}
