<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peneiras extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'Peneiras';
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
}
