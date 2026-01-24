<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // <--- IMPORTANTE: Adicionado para imagem

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

    /**
     * [MUITO IMPORTANTE]
     * A lista $appends diz ao Laravel: "Sempre que converter este model para JSON (API),
     * inclua estes campos calculados extra".
     */
    protected $appends = ['foto_url_completa', 'sub_divisao'];

    // --- RELACIONAMENTOS ---
    public function pessoa()
    {
        return $this->belongsTo(Pessoas::class, 'pessoa_id');
    }

    // --- ACCESSORS (Campos Calculados) ---

    /**
     * Cria o campo 'sub_divisao' no JSON automaticamente
     */
    public function getSubDivisaoAttribute(): string
    {
        // Se a data de nascimento for nula, retorna string vazia para não quebrar
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
            default => 'Sem Sub Divisao', // ou 'Profissional' dependendo da regra
        };
    }

    /**
     * Cria o campo 'foto_url_completa' no JSON automaticamente.
     * Tenta usar o Storage::url (padrão Laravel) ou monta a URL na mão se falhar.
     */
    public function getFotoUrlCompletaAttribute()
    {
        if ($this->foto_perfil_url) {
            // [MODO MANUAL] Monta o link direto do Google
            // Isso resolve o erro "Driver does not support retrieving URLs"
            $bucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');
            return "https://storage.googleapis.com/{$bucket}/{$this->foto_perfil_url}";
        }
        
        return null;
    }
}