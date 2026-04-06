<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;
use App\Models\Servicio;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $odontologia = Especialidad::create([
            'nombre' => 'Odontología',
            'descripcion' => 'Especialidad encargada de la salud bucodental.',
            'activo' => true,
        ]);

        Servicio::create([
            'especialidad_id' => $odontologia->id,
            'nombre' => 'Limpieza Dental Básica',
            'descripcion' => 'Limpieza con ultrasonido y pulido profiláctico.',
            'duracion_minutos' => 30,
            'precio' => 25.00,
        ]);

        Servicio::create([
            'especialidad_id' => $odontologia->id,
            'nombre' => 'Extracción de Cordal',
            'descripcion' => 'Procedimiento quirúrgico para la remoción de muelas del juicio.',
            'duracion_minutos' => 60,
            'precio' => 75.00,
        ]);

        $psicologia = Especialidad::create([
            'nombre' => 'Psicología',
            'descripcion' => 'Atención profesional para la salud mental y emocional.',
            'activo' => true,
        ]);

        Servicio::create([
            'especialidad_id' => $psicologia->id,
            'nombre' => 'Terapia Individual',
            'descripcion' => 'Sesión presencial o virtual de 45 minutos.',
            'duracion_minutos' => 45,
            'precio' => 30.00,
        ]);

        $medicina = Especialidad::create([
            'nombre' => 'Medicina General',
            'descripcion' => 'Atención primaria, evaluación y diagnóstico preventivo.',
            'activo' => true,
        ]);

        Servicio::create([
            'especialidad_id' => $medicina->id,
            'nombre' => 'Consulta General',
            'descripcion' => 'Evaluación de síntomas, toma de presión y receta médica.',
            'duracion_minutos' => 30,
            'precio' => 20.00,
        ]);
    }
}