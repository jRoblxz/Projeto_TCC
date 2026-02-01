<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treinadores extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'treinadores';
    protected $fillable = [
        'pessoa_id',
        'clube_organizacao',
        'cargo',
        'cref',
        'biografia_resumo',
        'anos_experiencia',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoas::class, 'pessoa_id');
    }
}
