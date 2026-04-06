<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesional_bloqueos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('motivo')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'fecha_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesional_bloqueos');
    }
};
