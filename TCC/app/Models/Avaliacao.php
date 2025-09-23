<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'Avaliacoes';

    protected $fillable = [
        'jogador_id',
        'treinador_id',
        'peneira_id',
        'nota',
        'observacoes',
        'data_avaliacao'
    ];

    public $timestamps = false; // Sua tabela usa data_avaliacao ao invés de created_at/updated_at

    // Relacionamento com jogador
    public function jogador()
    {
        return $this->belongsTo(Player::class, 'jogador_id');
    }
}