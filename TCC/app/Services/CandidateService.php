<?php

namespace App\Services;

use App\Models\Pessoas;
use App\Models\Jogadores;
use App\Models\Inscricoes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;

class CandidateService
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function register(array $data, ?UploadedFile $photo): array
    {
        return DB::transaction(function () use ($data, $photo) {
            // 1. Upload da Foto
            $path = null;
            if ($photo) {
                $path = $this->fileService->upload($photo, 'user');
            }

            // 2. Criar Pessoa
            $pessoa = Pessoas::create([
                'nome_completo' => $data['nome_completo'],
                'email' => $data['email'],
                'cpf' => $data['cpf'],
                'rg' => $data['rg'] ?? null,
                'telefone' => $data['telefone'] ?? null,
                'cidade' => $data['cidade'] ?? null,
                'data_nascimento' => $data['data_nascimento'],
                'foto_perfil_url' => $path
            ]);

            // 3. Criar Jogador
            $jogador = Jogadores::create([
                'pessoa_id' => $pessoa->id,
                'posicao_principal' => $data['posicao_principal'],
                'posicao_secundaria' => $data['posicao_secundaria'] ?? null,
                'pe_preferido' => $data['pe_preferido'] ?? null,
                'altura_cm' => $data['altura_cm'] ?? null,
                'peso_kg' => $data['peso_kg'] ?? null,
                'historico_lesoes_cirurgias' => $data['historico_lesoes_cirurgias'] ?? 'nao',
                'video_apresentacao_url' => $data['video_apresentacao_url'] ?? null,
            ]);

            // 4. Inscrever na Peneira
            Inscricoes::create([
                'jogador_id' => $jogador->id,
                'peneira_id' => $data['peneira_id'],
                'data_inscricao' => now(),
            ]);

            // 5. Criar Usuário de Acesso
            User::create([
                'name' => $pessoa->nome_completo,
                'email' => $pessoa->email,
                'password' => Hash::make($data['cpf']),
                'role' => 'candidato',
            ]);

            return ['pessoa' => $pessoa, 'jogador' => $jogador];
        });
    }
}