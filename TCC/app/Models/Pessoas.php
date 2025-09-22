<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pessoas extends Model
{
    protected $table = 'Pessoas';
    protected $fillable = [
        'nome_completo',
        'idade',
        'data_nascimento',
        'cidade',
        'cpf',
        'rg',
        'email',
        'telefone',
        'foto_perfil_url',
    ];
}
