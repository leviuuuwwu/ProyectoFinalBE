<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Especialidad extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'especialidades';

    protected $fillable = ['nombre', 'descripcion', 'activo'];

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }
}