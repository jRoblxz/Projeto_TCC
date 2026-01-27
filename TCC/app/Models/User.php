<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <--- 1. VOCÊ PRECISA DESSA LINHA AQUI EM CIMA

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    
    // 2. E PRECISA DESSA LINHA AQUI DENTRO (Repare no HasApiTokens no começo)
    use HasApiTokens, HasFactory, Notifiable; 

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        ];
    }
}