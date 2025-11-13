<?php

namespace App\Models;

// ... outros 'use'
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject; // Importe isso

class User extends Authenticatable implements JWTSubject // Implemente isso
{
    // ... seu 'use HasApiTokens, HasFactory, Notifiable' etc.

    // [IMPORTANTE] Adicione 'role' ao $fillable
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Adicione isso
    ];

    // ...

    // [NOVO] Adicione estes dois métodos obrigatórios do JWT
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // Aqui adicionamos o 'role' ao token
        return [
            'role' => $this->role,
            'name' => $this->name, // Pode adicionar o que mais quiser
        ];
    }
}