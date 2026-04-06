<?php

namespace Tests\Feature;

use App\Models\Especialidad;
use App\Models\Servicio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_especialidades_responde_200_y_estructura(): void
    {
        $esp = Especialidad::create([
            'nombre' => 'Cardiología',
            'descripcion' => 'Test',
            'activo' => true,
        ]);

        Servicio::create([
            'especialidad_id' => $esp->id,
            'nombre' => 'Consulta',
            'descripcion' => null,
            'duracion_minutos' => 30,
            'precio' => 50.00,
            'activo' => true,
        ]);

        $response = $this->getJson('/api/especialidades');

        $response->assertOk()
            ->assertJsonStructure(['data']);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('Cardiología', $data[0]['nombre']);
        $this->assertArrayHasKey('servicios', $data[0]);
    }

    public function test_servicios_responde_200(): void
    {
        $esp = Especialidad::create([
            'nombre' => 'Dermatología',
            'descripcion' => null,
            'activo' => true,
        ]);

        Servicio::create([
            'especialidad_id' => $esp->id,
            'nombre' => 'Revisión',
            'duracion_minutos' => 20,
            'precio' => 40.00,
            'activo' => true,
        ]);

        $response = $this->getJson('/api/servicios');

        $response->assertOk()
            ->assertJsonStructure(['data']);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('Revisión', $data[0]['nombre']);
    }
}
