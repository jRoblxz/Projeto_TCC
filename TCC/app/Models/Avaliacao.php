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
        'jogador_id', 'treinador_id', 'peneira_id', 'nota', 'observacoes', 'data_avaliacao',
        'tecnica', 'condicionamento', 'finalizacao', 'velocidade', 'posicionamento', 'cabeceio',
        // NOVOS:
        'reflexo', 'saida_goleiro', 'jogo_aereo', 'um_contra_um', 'fisico', 
        'marcacao', 'desarme', 'passe', 'cruzamento', 'visao_jogo'
    ];

    // Relacionamento com jogador
    public function jogador()
    {
        return $this->belongsTo(Jogadores::class, 'jogador_id');
    }
}
