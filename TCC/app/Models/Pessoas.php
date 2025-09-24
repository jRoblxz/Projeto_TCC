<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    // Converter data_nascimento para Carbon automaticamente
    protected $casts = [
        'data_nascimento' => 'date',
    ];

    // Accessor para calcular a idade automaticamente
    public function getIdadeAttribute()
    {
        if (!$this->data_nascimento) {
            return null;
        }
        
        return $this->data_nascimento->age;
    }

    // Ou se preferir um método mais explícito:
    public function calcularIdade()
    {
        if (!$this->data_nascimento) {
            return null;
        }
        
        return Carbon::parse($this->data_nascimento)->age;
    }
}
