<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscricoes extends Model
{
    public $timestamps = false;

    protected $table = 'Inscricoes';
    protected $fillable = [
        'jogador_id',
        'peneira_id',
        'data_inscricao',
    ];

    public function jogador()
    {
        return $this->belongsTo(Jogadores::class, 'jogador_id');
    }

    public function peneira()
    {
        return $this->belongsTo(Peneiras::class, 'peneira_id');
    }

    public function getNumeroInscricaoAttribute()
    {
        return 'PEN-' . now()->year . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
