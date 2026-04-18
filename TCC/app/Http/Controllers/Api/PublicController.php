<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CandidateService;
use App\Services\PeneiraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicController extends Controller
{
    protected $candidateService;
    protected $peneiraService;

    // Injeção de Dependência
    public function __construct(CandidateService $candidateService, PeneiraService $peneiraService)
    {
        $this->candidateService = $candidateService;
        $this->peneiraService = $peneiraService;
    }

    public function getOpenPeneiras()
    {
        // Usa o service existente (ou cria um método específico lá)
        $peneiras = \App\Models\Peneiras::whereIn('status', ['AGENDADA', 'EM_ANDAMENTO'])
            ->select('id', 'title', 'date', 'local', 'subdivision', 'status')
            ->orderByDesc('date')
            ->get();

        return response()->json($peneiras);
    }

    public function registerCandidate(Request $request)
    {
        $validated = $request->validate([
            'nome_completo' => 'required|string',
            'email' => 'required|email|unique:pessoas,email',
            'cpf' => 'required|string|unique:pessoas,cpf',
            'peneira_id' => 'required|exists:peneiras,id',
            'data_nascimento' => 'required|date',
            'posicao_principal' => 'required|string',
            'foto_perfil_url' => 'nullable|file|image|max:5120',
            // ... valide os outros campos opcionais aqui se necessário
        ]);

        try {
            // A Controller apenas delega para o Service
            $this->candidateService->register(
                $request->all(),
                $request->file('foto_perfil_url')
            );

            return response()->json(['message' => 'Inscrição realizada com sucesso!'], 201);
        } catch (\Exception $e) {
            Log::error("Erro no registro: " . $e->getMessage());
            // Em produção, evite enviar $e->getMessage() direto para o user, use msg genérica
            return response()->json(['error' => 'Erro interno ao processar inscrição: ' . $e->getMessage()], 500);
        }
    }

    public function getAvailableForMe()
    {
        // 1. Verifica se o usuário está logado
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Sessão expirada ou usuário não autenticado.'], 401);
        }

        // 2. Verifica se o usuário tem um registro na tabela 'pessoas'
        // O erro "on null" acontece se $user->pessoa for nulo aqui
        if (!$user->pessoa) {
            return response()->json(['error' => 'Perfil de pessoa não encontrado para este usuário.'], 404);
        }

        $jogador = $user->pessoa->jogador;
        if (!$jogador) {
            return response()->json(['error' => 'Perfil de jogador não configurado.'], 404);
        }

        $minhaSub = $user->pessoa->sub_divisao; // Use o nome exato da sua coluna

        // IDs das peneiras que ele já participa
        $inscritoIds = \App\Models\Inscricoes::where('jogador_id', $jogador->id)->pluck('peneira_id');

        $query = \App\Models\Peneiras::whereIn('status', ['AGENDADA', 'EM_ANDAMENTO', 'Agendada', 'Em Andamento'])
            ->whereNotIn('id', $inscritoIds);

        // Lógica de Filtro por Categoria
        if ($minhaSub && $minhaSub !== 'Sem Sub Divisao') {
            $query->where('sub_divisao', $minhaSub);
        } else {
            $query->where(function ($q) {
                $q->where('sub_divisao', 'Geral')
                    ->orWhere('sub_divisao', 'Sem Sub Divisao')
                    ->orWhereNull('sub_divisao');
            });
        }

        return response()->json($query->get());
    }

    public function getMyEnrollments()
    {
        $user = auth()->user();
        
        // Verifica se o usuário e o jogador existem
        if (!$user || !$user->pessoa || !$user->pessoa->jogador) {
            return response()->json([]);
        }

        $jogadorId = $user->pessoa->jogador->id;

        // Usa Query Builder para buscar com precisão (Evita erros de Relacionamento no Model)
        $inscricoes = \Illuminate\Support\Facades\DB::table('inscricoes')
            ->join('peneiras', 'inscricoes.peneira_id', '=', 'peneiras.id')
            ->where('inscricoes.jogador_id', $jogadorId)
            ->select(
                'peneiras.id', 
                'peneiras.nome_evento', // Se no seu BD for title, ele converte pra nome_evento pro React ler
                'peneiras.data_evento', 
                'peneiras.local', 
                'peneiras.sub_divisao',
                'inscricoes.status'
            )
            ->get();

        return response()->json($inscricoes);
    }

    // ---> FUNÇÃO 2: Faz a Inscrição Rápida (1 clique)
    public function quickEnroll(Request $request)
    {
        $user = auth()->user();
        
        // Tenta pegar o jogador pelo relacionamento direto
        $jogador = $user->pessoa->jogador ?? null;

        // SEGUNDA CHANCE: Se não achou pelo ID, tenta pelo e-mail do usuário logado
        if (!$jogador) {
            $jogador = \App\Models\Jogadores::whereHas('pessoa', function($q) use ($user) {
                $q->where('email', $user->email);
            })->first();
        }

        // Se mesmo assim não achar, aí sim retornamos o erro
        if (!$jogador) {
            return response()->json(['error' => 'Perfil de jogador não encontrado.'], 404);
        }

        // ... resto da lógica de verificação de duplicidade e criação da inscrição ...
        \App\Models\Inscricoes::create([
            'jogador_id' => $jogador->id,
            'peneira_id' => $request->peneira_id,
            'data_inscricao' => now(),
            'status' => 'pendente'
        ]);

        return response()->json(['message' => 'Inscrição realizada com sucesso!']);
    }
}
