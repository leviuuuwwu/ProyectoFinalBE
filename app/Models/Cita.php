<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Cita extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Cita $cita) {
            if (empty($cita->uuid)) {
                $cita->uuid = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'uuid',
        'paciente_id',
        'medico_id',
        'servicio_id',
        'fecha_hora',
        'motivo',
        'estado',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }
}
