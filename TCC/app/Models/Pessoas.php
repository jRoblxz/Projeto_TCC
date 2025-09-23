<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pessoas extends Model
{
    public $timestamps = false;

    protected $table = 'Pessoas';

    protected $fillable = [
        'nome_completo',
        'idade',
        'data_nascimento',
        'cidade',
        'cpf',
        'rg',
        'telefone',
        'email',
        'senha',
        'foto_perfil_url',

    ];
}
