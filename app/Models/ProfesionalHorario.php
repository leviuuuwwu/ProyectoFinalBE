<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfesionalHorario extends Model
{
    use HasUuids;

    protected $table = 'profesional_horarios';

    protected $fillable = [
        'user_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'intervalo_minutos',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
