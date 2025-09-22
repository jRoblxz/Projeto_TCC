<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jogadores extends Model
{
    protected $table = 'jogadores';
    protected $fillable = [
        'id_pessoa',
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
        return $this->belongsTo(Pessoas::class, 'id_pessoa');
    }
}
