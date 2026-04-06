<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids; 

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'password',
        'rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function profesionalHorarios()
    {
        return $this->hasMany(ProfesionalHorario::class, 'user_id');
    }

    public function profesionalBloqueos()
    {
        return $this->hasMany(ProfesionalBloqueo::class, 'user_id');
    }

    public function citasComoMedico()
    {
        return $this->hasMany(Cita::class, 'medico_id');
    }
}