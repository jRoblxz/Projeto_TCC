<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;

    protected $table = 'Equipes'; 

    public $timestamps = false; 

    protected $fillable = [
        'nome',
        'peneira_id',
    ];


    public function peneira()
    {

        return $this->belongsTo(Peneiras::class, 'peneira_id');
    }

    public function jogadores()
    {
        return $this->belongsToMany(
            Jogadores::class,
            'JogadoresPorEquipe', // Nome da tabela pivot
            'equipe_id',          // Chave estrangeira da Equipe
            'jogador_id'          // Chave estrangeira do Jogador
        )->withPivot(['posicao_campo_x', 'posicao_campo_y', 'titular']); // [CRUCIAL]
    }
}