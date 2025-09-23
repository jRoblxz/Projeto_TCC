<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; //joao criou testes
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $table = 'Jogadores'; // Nome da tabela no banco

    protected $fillable = [
        'pessoa_id',
        'pe_preferido',
        'posicao_principal',
        'posicao_secundaria',
        'altura_cm',
        'peso_kg',
        'historico_lesoes_cirurgias',
        'video_apresentacao_url',
        'video_skills_url'
    ];

    // Relacionamento com avaliações
    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class, 'jogador_id');
    }

    // Relacionamento com pessoa
    public function pessoa()
    {
        return $this->belongsTo(Pessoas::class, 'pessoa_id');
    }

    // Pegar a última avaliação
    public function getUltimaAvaliacaoAttribute()
    {
        return $this->avaliacoes()->latest('data_avaliacao')->first();
    }

    // Pegar o rating médio das avaliações
    public function getRatingMedioAttribute()
    {
        return round($this->avaliacoes()->avg('nota') ?? 0);
    }
}
