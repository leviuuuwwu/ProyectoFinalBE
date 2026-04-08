<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignUuid('medico_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('paciente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('servicio_id')->nullable()->constrained('servicios')->nullOnDelete();
            $table->dateTime('fecha_hora');
            $table->unsignedSmallInteger('duracion_minutos')->default(30);
            $table->string('motivo')->nullable();
            $table->string('estado', 32)->default('Programada');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['medico_id', 'fecha_hora']);
            $table->index(['paciente_id', 'estado']);
            $table->index(['medico_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
