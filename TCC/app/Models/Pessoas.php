<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Pessoas extends Model
{
    use HasFactory;

    protected $table = 'Pessoas';

    protected $fillable = [
        'nome_completo',
        'data_nascimento',
        'cpf',
        'rg',
        'telefone',
        'email',
        'senha',
        'foto_perfil_url',
        'tipo_usuario',
        'data_criacao'
    ];

    // Relacionamento com jogador
    public function jogador()
    {
        return $this->hasOne(Player::class, 'pessoa_id');
    }
}
