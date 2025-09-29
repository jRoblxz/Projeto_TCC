<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peneiras extends Model
{
    public $timestamps = false;

    protected $table = 'Peneiras';
    protected $fillable = [
        'nome_evento',
        'data_evento',
    ];
}
