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
    protected $casts = [
        'data_nascimento' => 'date', // ou 'datetime' se tiver hora
    ];

    public function pessoa()
{
    return $this->belongsTo(Pessoas::class, 'pessoa_id'); // Adapte as chaves se necessário
}

    public function getSubDivisaoAttribute(): string
    {
        $idade = $this->data_nascimento->age ?? '';

        // A estrutura 'match' é uma versão moderna e mais legível do 'switch'
        $subDivisao = match ($idade) {
            6, 7 => 'Sub-7',
            8, 9 => 'Sub-9',
            10, 11 => 'Sub-11',
            12, 13 => 'Sub-13',
            14, 15 => 'Sub-15',
            16, 17 => 'Sub-17',
            18, 19, 20 => 'Sub-20',
            default => 'Sem Sub Divisao',
        };
        return $subDivisao;
    }

    public function getFotoPerfilUrlCompleteAttribute()
    {
        if ($this->foto_perfil_url) {
            return 'https://storage.googleapis.com/' . env('GOOGLE_CLOUD_STORAGE_BUCKET') . '/' . $this->foto_perfil_url;
        }
        return null;
    }
    // Converter data_nascimento para Carbon automaticamente
    /*protected $casts = [
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
    }*/
}
