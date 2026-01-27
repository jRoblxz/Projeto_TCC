<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jogadores extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'Jogadores';
    
    // [CORREÇÃO 1] Adiciona o rating aos dados retornados no JSON
    protected $appends = ['rating_medio', 'posicao_abreviada'];

    protected $fillable = [
        'pessoa_id', 'pe_preferido', 'posicao_principal',
        'posicao_secundaria', 'historico_lesoes_cirurgias',
        'altura_cm', 'peso_kg', 'video_apresentacao_url',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoas::class, 'pessoa_id');
    }

    public function avaliacoes()
    {
        return $this->hasOne(Avaliacao::class, 'jogador_id');
    }

    public function ultima_avaliacao()
    {
        return $this->hasOne(Avaliacao::class, 'jogador_id')->latestOfMany();
    }

    // [CORREÇÃO 2] Accessor Inteligente
    // Se o controller usou withAvg(), usa esse valor.
    // Se não, calcula na hora (evita erro de nulo).
    public function getRatingMedioAttribute()
    {
        // Se o atributo já existe (veio da query SQL), retorna ele arredondado
        if (array_key_exists('rating_medio', $this->attributes)) {
            return round($this->attributes['rating_medio'], 1); // 1 casa decimal
        }

        // Fallback: calcula se não veio da query
        return round($this->avaliacoes()->avg('nota') ?? 0, 1);
    }

    public function getPosicaoAbreviadaAttribute(): string
    {
        $posicao = $this->posicao_principal ?? '';
        return match ($posicao) {
            'Goleiro' => 'GOL',
            'Zagueiro', 'Zagueiro Direito', 'Zagueiro Esquerdo' => 'ZAG',
            'Lateral', 'Lateral Direito', 'Lateral Esquerdo' => 'LAT',
            'Volante' => 'VOL',
            'Meia', 'Meia Atacante', 'Meia Armador' => 'MEI',
            'Atacante', 'Centroavante', 'Ponta' => 'ATA',
            default => substr($posicao, 0, 3),
        };
    }

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