<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_devuelve_token_y_201(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'nombre' => 'Test',
            'apellido' => 'User',
            'email' => 'nuevo@test.test',
            'telefono' => '5551234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Usuario registrado exitosamente')
            ->assertJsonStructure(['data', 'access_token', 'token_type']);

        $this->assertNotEmpty($response->json('access_token'));
        $this->assertDatabaseHas('users', ['email' => 'nuevo@test.test']);
    }

    public function test_login_exitoso_con_token(): void
    {
        User::factory()->create([
            'email' => 'login@test.test',
            'password' => Hash::make('secretpass'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login@test.test',
            'password' => 'secretpass',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Login exitoso')
            ->assertJsonStructure(['data', 'access_token', 'token_type']);

        $this->assertNotEmpty($response->json('access_token'));
    }

    public function test_login_credenciales_invalidas_422(): void
    {
        User::factory()->create([
            'email' => 'x@test.test',
            'password' => Hash::make('correct'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'x@test.test',
            'password' => 'wrong',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_profile_requiere_autenticacion(): void
    {
        $this->getJson('/api/user/profile')->assertUnauthorized();
    }

    public function test_profile_con_bearer_devuelve_usuario(): void
    {
        $user = User::factory()->create(['email' => 'perfil@test.test']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/user/profile');

        $response->assertOk()
            ->assertJsonPath('data.email', 'perfil@test.test');
    }

    public function test_logout_revoca_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/auth/logout');

        $response->assertOk()
            ->assertJsonPath('message', 'Cierre de sesión exitoso');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
