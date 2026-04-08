<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignUuid('paciente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('medico_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('fecha_hora');
            $table->string('motivo');
            $table->string('estado');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index(['paciente_id', 'estado']);
            $table->index(['medico_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};

