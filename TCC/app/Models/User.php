<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Pessoas; 

class User extends Authenticatable
{
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

    /**
     * [SOLUÇÃO MÁGICA SEM MEXER NO BANCO]
     * Relacionamento: O User tem uma Pessoa.
     * Como não temos IDs de vínculo, usamos o E-MAIL para ligar as duas tabelas.
     */
    public function pessoa()
    {
        // 1º Param: Model destino (Pessoas)
        // 2º Param: Chave estrangeira na tabela Pessoas ('email')
        // 3º Param: Chave local na tabela Users ('email')
        return $this->hasOne(Pessoas::class, 'email', 'email');
    }
}