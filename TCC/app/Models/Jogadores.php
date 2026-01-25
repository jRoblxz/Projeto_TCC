<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use PhpParser\Node\Expr\Cast\Void_;

class Jogadores extends Model
{

    use HasFactory;
    public $timestamps = false;

    protected $table = 'Jogadores';
    protected $fillable = [
        'pessoa_id',
        'pe_preferido',
        'posicao_principal',
        'posicao_secundaria',
        'historico_lesoes_cirurgias',
        'altura_cm',
        'peso_kg',
        'video_apresentacao_url',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoas::class, 'pessoa_id');
    }

    // Relacionamento com avaliações ------
    public function avaliacoes()
    {
        return $this->hasOne(Avaliacao::class, 'jogador_id');
    }

    public function ultima_avaliacao()
    {
        // Pega a avaliação mais recente baseada no ID ou data
        // Requer Laravel 8.42+ para usar latestOfMany()
        return $this->hasOne(Avaliacao::class, 'jogador_id')->latestOfMany();
        
        // SE DER ERRO COM latestOfMany, USE ESTA VERSÃO ANTIGA:
        // return $this->hasOne(Avaliacao::class, 'jogador_id')->latest();
    }
    public function getUltimaAvaliacaoAttribute()
    {
        return $this->avaliacoes()->latest('data_avaliacao')->first();
    }

    public function getRatingMedioAttribute()
    {
        return round($this->avaliacoes()->avg('nota') ?? 0);
    }

    public function getPosicaoAbreviadaAttribute(): string
    {
        $posicao = $this->posicao_principal ?? '';

        // A estrutura 'match' é uma versão moderna e mais legível do 'switch'
        $abreviacao = match ($posicao) {
            'Goleiro' => 'GOL',
            'Zagueiro Direito' => 'ZGD',
            'Zagueiro Esquerdo' => 'ZGE',
            'Lateral Direito' => 'LTD',
            'Lateral Esquerdo' => 'LTE',
            'Volante' => 'VOL',
            'Meia' => 'MEI',
            'Ponta Direita' => 'PD',
            'Ponta Esquerda' => 'PE',
            'Centroavante', 'Atacante' => 'ATA', // Múltiplas opções para o mesmo resultado
            default => strtoupper(substr($posicao, 0, 3)), // Lógica antiga como fallback
        };

        return $abreviacao;
    }
    // ----------------------------
    public function equipes()
    {
        return $this->belongsToMany(
            Equipe::class, 
            'JogadoresPorEquipe', 
            'jogador_id', 
            'equipe_id'
        )->withPivot(['posicao_campo_x', 'posicao_campo_y', 'titular']);
    }
}
