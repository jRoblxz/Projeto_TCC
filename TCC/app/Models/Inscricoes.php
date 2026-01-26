<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscricoes extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'inscricoes';
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
