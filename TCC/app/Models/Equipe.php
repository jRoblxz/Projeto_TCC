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
        'nome_equipe',
        'peneira_id',

    ];


    public function peneira()
    {
        return $this->belongsTo(Peneiras::class, 'peneira_id');
    }
}
