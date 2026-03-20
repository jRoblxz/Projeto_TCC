<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'avaliacoes';
    protected $fillable = [
        'jogador_id',
        'treinador_id',
        'peneira_id',
        'nota', // A nota geral continuará existindo, mas será a média das outras 6
        'tecnica',
        'condicionamento',
        'finalizacao',
        'velocidade',
        'posicionamento',
        'cabeceio',
        'observacoes',
        'data_avaliacao'
    ];

    // Relacionamento com jogador
    public function jogador()
    {
        return $this->belongsTo(Jogadores::class, 'jogador_id');
    }
}
