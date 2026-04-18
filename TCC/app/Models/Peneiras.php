<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peneiras extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'peneiras';
    protected $fillable = [
        'nome_evento',
        'data_evento',
        'local',
        'descricao',
        'status',
        'sub_divisao',
    ];

    public function inscricoes()
    {
        return $this->hasMany(\App\Models\Inscricoes::class, 'peneira_id');
    }

    public function avaliacoes() {
        return $this->hasMany(Avaliacao::class, 'peneira_id');
    }
    // Se o seu Admin lista os JOGADORES direto, você precisa desta relação (Many-to-Many):
    public function jogadores()
    {
        return $this->belongsToMany(Jogadores::class, 'inscricoes', 'peneira_id', 'jogador_id')
                    ->withPivot('status', 'data_inscricao');
    }

    // --- ADICIONE ISTO ---
    public function equipes() {
        // Supondo que seu Model se chame 'Equipes' ou 'Team'
        // Se o nome do arquivo for Team.php, mude para \App\Models\Team::class
        return $this->hasMany(\App\Models\Equipe::class, 'peneira_id');
    }
}