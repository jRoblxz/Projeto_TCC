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
        // COMO DEVE FICAR (Certo)
        return $this->belongsToMany(Jogadores::class, 'jogadoresporequipe', 'equipe_id', 'jogador_id')
            ->withPivot(['posicao_campo_x', 'posicao_campo_y', 'titular']);
        // Nota: Verifique se no seu banco é 'jogadoresporequipe' ou 'jogadores_por_equipe' e use o correto.
    }
}
