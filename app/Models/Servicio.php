<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Servicio extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'especialidad_id', 'nombre', 'descripcion', 'duracion_minutos', 'precio', 'activo'
    ];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }
}