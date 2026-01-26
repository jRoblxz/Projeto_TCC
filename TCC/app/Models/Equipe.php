<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;
    public $timestamps = false; // Se sua tabela não tiver created_at/updated_at

    protected $table = 'equipes'; // Confirme o nome da tabela
    protected $fillable = ['nome', 'peneira_id'];

    public function jogadores()
    {
        return $this->belongsToMany(
            Jogadores::class, 
            'JogadoresPorEquipe', // Nome da tabela pivô
            'equipe_id', 
            'jogador_id'
        )
        // [IMPORTANTE] Isso permite acessar $jogador->pivot->posicao_campo_x no Service
        ->withPivot(['posicao_campo_x', 'posicao_campo_y', 'titular']); 
    }
}