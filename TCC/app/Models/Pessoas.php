<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Pessoas extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $table = 'Pessoas';

    protected $fillable = [
        'nome_completo',
        'idade',
        'data_nascimento',
        'cidade',
        'cpf',
        'rg',
        'telefone',
        'email',
        'senha',
        'foto_perfil_url',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    protected $appends = ['foto_url_completa', 'sub_divisao'];

    // --- RELACIONAMENTOS ---

    // [IMPORTANTE] Este método estava faltando ou incorreto no seu arquivo
    public function jogador()
    {
        // Uma Pessoa "Tem Um" Jogador vinculado a ela.
        // O Laravel vai procurar a coluna 'pessoa_id' na tabela 'Jogadores'.
        return $this->hasOne(Jogadores::class, 'pessoa_id');
    }

    // --- ACCESSORS (Mantenha os que já existem) ---

    public function getSubDivisaoAttribute(): string
    {
        if (!$this->data_nascimento) return 'N/A';
        $idade = $this->data_nascimento->age;

        return match ($idade) {
            6, 7 => 'Sub-7',
            8, 9 => 'Sub-9',
            10, 11 => 'Sub-11',
            12, 13 => 'Sub-13',
            14, 15 => 'Sub-15',
            16, 17 => 'Sub-17',
            18, 19, 20 => 'Sub-20',
            default => 'Sem Sub Divisao',
        };
    }

    public function getFotoUrlCompletaAttribute()
    {
        if ($this->foto_perfil_url) {
            $bucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');
            return "https://storage.googleapis.com/{$bucket}/{$this->foto_perfil_url}";
        }
        return null;
    }
}