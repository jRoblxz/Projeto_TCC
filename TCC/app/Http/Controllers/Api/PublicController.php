<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peneiras;
use App\Models\Pessoas;
use App\Models\Jogadores;
use App\Models\Inscricoes;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
// Importe o Client do Google Cloud se for usar o upload direto aqui, 
// ou idealmente mova o upload para um FileUploadService.

class PublicController extends Controller
{
    // Listar peneiras abertas para o Select do formulário
    public function getOpenPeneiras()
    {
        $peneiras = Peneiras::whereIn('status', ['AGENDADA', 'EM_ANDAMENTO'])
            ->select('id', 'nome_evento', 'data_evento')
            ->orderByDesc('data_evento')
            ->get();
            
        return response()->json($peneiras);
    }

    // O antigo UserController@store
    public function registerCandidate(Request $request)
    {
        // 1. Validação (Pode criar um PublicCandidateRequest separado)
        $validated = $request->validate([
            'nome_completo' => 'required|string',
            'email' => 'required|email|unique:pessoas,email',
            'cpf' => 'required|string|unique:pessoas,cpf',
            'peneira_id' => 'required|exists:peneiras,id',
            // ... adicione os outros campos
        ]);

        DB::beginTransaction();
        try {
            $path = null;
            // 2. Lógica de Upload (Simplificada para exemplo)
            if ($request->hasFile('foto_perfil_url')) {
                // Reutilize sua lógica do Google Cloud Storage aqui
                // Sugestão: Crie um método privado ou Service para isso
                // $path = ... upload logic ...
            }

            // 3. Criações
            $pessoa = Pessoas::create([
                'nome_completo' => $request->nome_completo,
                'email' => $request->email,
                'cpf' => $request->cpf,
                // ... outros campos
                'foto_perfil_url' => $path
            ]);

            $jogador = Jogadores::create([
                'pessoa_id' => $pessoa->id,
                'posicao_principal' => $request->posicao_principal,
                // ...
            ]);

            Inscricoes::create([
                'jogador_id' => $jogador->id,
                'peneira_id' => $request->peneira_id,
                'data_inscricao' => now(),
            ]);

            User::create([
                'name' => $pessoa->nome_completo,
                'email' => $pessoa->email,
                'password' => Hash::make($request->cpf), // Senha padrão = CPF
                'role' => 'candidato',
            ]);

            DB::commit();
            return response()->json(['message' => 'Inscrição realizada com sucesso!'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro no registro: " . $e->getMessage());
            return response()->json(['error' => 'Erro interno ao processar inscrição'], 500);
        }
    }
}