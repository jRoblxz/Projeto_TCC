<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{

    public $timestamps = false;

    protected $table = 'Avaliacoes';
    protected $fillable = [
        'jogador_id',
        'treinador_id',
        'peneira_id',
        'nota',
        'observacoes',
        'data_avaliacao'
    ];

    // Relacionamento com jogador
    public function jogador()
    {
        return $this->belongsTo(Jogadores::class, 'jogador_id');
    }
}