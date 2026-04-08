<?php

namespace Tests\Unit;

use App\Models\Cita;
use App\Models\User;
use App\Policies\CitaPolicy;
use PHPUnit\Framework\TestCase;

class CitaPolicyTest extends TestCase
{
    private function makeUser($id, $rol)
    {
        $user = new User();
        $user->forceFill(['rol' => $rol]);
        $user->id = $id;

        return $user;
    }

    private function makeCita($medicoId, $pacienteId)
    {
        $cita = new Cita();
        $cita->forceFill([
            'medico_id' => $medicoId,
            'paciente_id' => $pacienteId,
        ]);

        return $cita;
    }

    public function test_admin_puede_ver_todo()
    {
        $policy = new CitaPolicy();
        $admin = $this->makeUser('1', 'admin');

        $this->assertTrue($policy->before($admin, 'view'));
    }

    public function test_medico_puede_ver_su_cita()
    {
        $policy = new CitaPolicy();
        $medico = $this->makeUser('10', 'medico');
        $cita = $this->makeCita('10', '20');

        $this->assertTrue($policy->view($medico, $cita));
    }

    public function test_paciente_no_puede_ver_cita_ajena()
    {
        $policy = new CitaPolicy();
        $paciente = $this->makeUser('30', 'paciente');
        $cita = $this->makeCita('10', '20');

        $this->assertFalse($policy->view($paciente, $cita));
    }

    public function test_medico_puede_completar_su_cita()
    {
        $policy = new CitaPolicy();
        $medico = $this->makeUser('10', 'medico');
        $cita = $this->makeCita('10', '20');

        $this->assertTrue($policy->completar($medico, $cita));
    }

    public function test_paciente_no_puede_completar_cita()
    {
        $policy = new CitaPolicy();
        $paciente = $this->makeUser('30', 'paciente');
        $cita = $this->makeCita('10', '20');

        $this->assertFalse($policy->completar($paciente, $cita));
    }
}


