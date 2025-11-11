<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Inscricoes;
use App\Models\Jogadores;
use App\Models\Pessoas;
use Illuminate\Http\Request;
use App\Models\Peneiras;
use App\Models\User; // [1. NOVO] Importe o model User
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;   // [2. NOVO] Importe o DB
use Illuminate\Support\Facades\Hash; // [3. NOVO] Importe o Hash
use Illuminate\Support\Facades\Log;  // [4. NOVO] Importe o Log

class UserController
{
    public function instrucao()
    {
        return view('telas_forms.instrucao');
    }

    // [CORREÇÃO] Altere o método 'confirmacao'
    public function confirmacao(Request $request)
    {
        // 1. Pega o ID da inscrição que foi salvo na sessão
        $inscricaoId = $request->session()->get('inscricao_id');

        // 2. Se não houver ID (ex: usuário recarregou a página), não quebra
        if (!$inscricaoId) {
            // Você pode redirecionar para a home ou mostrar uma msg
            return redirect('/')->with('error', 'Sessão expirada.');
        }

        // 3. Busca a inscrição no banco de dados
        $inscricao = Inscricoes::findOrFail($inscricaoId);

        // 4. Envia a variável $inscricao para a view
        return view('telas_forms.confirmacao', compact('inscricao'));
    }

    public function create()
    {
        // [CORREÇÃO] Use 'AGENDADA' (maiúsculo)
        // Usei 'whereIn' para pegar também as que estão 'EM_ANDAMENTO'
        $peneiras = Peneiras::whereIn('status', ['AGENDADA', 'EM_ANDAMENTO'])
                            ->orderByDesc('data_evento') // Para mostrar as mais novas primeiro
                            ->get();

        return view('telas_forms.forms1', compact('peneiras'));
    }

    public function store(UserRequest $request)
    {
        // 1. Validar os dados
        $validatedData = $request->validated();

        $path = null;

        // 2. Iniciar a transação
        DB::beginTransaction();

        try {
            // 3. Salvar a foto
            if ($request->hasFile('foto_perfil_url')) {
                $path = $request->file('foto_perfil_url')->store('user', 'public');
            }

            // 4. Cadastrar o Pessoa
            $pessoa = Pessoas::create([
                'nome_completo' => $validatedData['nome_completo'],
                'data_nascimento' => $validatedData['data_nascimento'],
                'cidade' => $validatedData['cidade'],
                'cpf' => $validatedData['cpf'],
                'rg' => $validatedData['rg'],
                'email' => $validatedData['email'],
                'telefone' => $validatedData['telefone'],
                'foto_perfil_url' => $path,
            ]);

            // 5. Cadastrar o Jogador
            $jogador = Jogadores::create([
                'pessoa_id' => $pessoa->id,
                'pe_preferido' => $validatedData['pe_preferido'],
                'posicao_principal' => $validatedData['posicao_principal'],
                'posicao_secundaria' => $validatedData['posicao_secundaria'],
                'historico_lesoes_cirurgias' => $validatedData['historico_lesoes_cirurgias'],
                'altura_cm' => $validatedData['altura_cm'],
                'peso_kg' => $validatedData['peso_kg'],
                'video_apresentacao_url' => $validatedData['video_apresentacao_url'],
            ]);

            // 6. Cadastrar a Inscrição
            $inscricao = Inscricoes::create([
                'jogador_id' => $jogador->id,
                'peneira_id' => $validatedData['peneira_id'],
                'data_inscricao' => Carbon::now(),
            ]);

            // 7. [NOVO] Criar o Usuário para login
            $user = User::create([
                'name' => $pessoa->nome_completo,
                'email' => $pessoa->email,
                'password' => Hash::make($pessoa->cpf), // Usa o CPF como senha (CRIPTOGRAFADO)
                'role' => 'candidato',                  // Define o 'role'
            ]);

            // 8. Se tudo deu certo, comita a transação
            DB::commit();

            // 9. [CORREÇÃO] Salva o ID da inscrição na sessão
            $request->session()->flash('inscricao_id', $inscricao->id);

            // 10. Redirecionar para a tela de confirmação
            return redirect()->route('tela.confirmacao');

        } catch (\Exception $e) {
            // 10. Se algo deu errado, desfaz tudo (Rollback)
            DB::rollBack();

            // Loga o erro para você poder investigar
            Log::error('Erro ao salvar inscrição: ' . $e->getMessage());

            // Retorna para o formulário com a mensagem de erro
            return redirect()->back()
                             ->withErrors(['submit' => 'Houve um erro ao processar sua inscrição. Tente novamente.'])
                             ->withInput(); // Mantém os dados no formulário
        }
    }
    
}