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
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;

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
            // 3. Salvar a foto usando o SDK do Google Cloud DIRETAMENTE
            if ($request->hasFile('foto_perfil_url')) {

                $arquivo = $request->file('foto_perfil_url');

                // Gera nome único para o arquivo
                $nomeDoArquivo = 'user/' . time() . '_' . uniqid() . '.' . $arquivo->getClientOriginalExtension();

                // 🎯 SOLUÇÃO DEFINITIVA: Usa o SDK do Google Cloud diretamente
                // Isso ignora completamente o Flysystem e suas tentativas de aplicar ACL
                $client = new \Google\Cloud\Storage\StorageClient([
                    'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
                    'keyFilePath' => env('GOOGLE_CLOUD_KEY_FILE'),
                ]);

                $bucket = $client->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));

                // Upload do arquivo SEM nenhum parâmetro de ACL
                $object = $bucket->upload(
                    file_get_contents($arquivo->getRealPath()),
                    [
                        'name' => $nomeDoArquivo,
                        // NÃO incluir 'predefinedAcl' ou qualquer configuração de ACL
                        // Com Uniform Bucket Access, as permissões são gerenciadas no nível do bucket
                    ]
                );

                $path = $nomeDoArquivo;
                Log::info('Arquivo enviado com sucesso via SDK direto: ' . $path);
            }

            // 4. Cadastrar a Pessoa
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

            // 7. Criar o Usuário
            $user = User::create([
                'name' => $pessoa->nome_completo,
                'email' => $pessoa->email,
                'password' => Hash::make($pessoa->cpf),
                'role' => 'candidato',
            ]);

            // 8. Comitar a transação
            DB::commit();

            // 9. Salvar o ID na sessão e redirecionar
            $request->session()->flash('inscricao_id', $inscricao->id);
            return redirect()->route('tela.confirmacao');
        } catch (\Exception $e) {
            // Desfaz a transação em caso de erro
            DB::rollBack();

            Log::error('Erro ao salvar inscrição: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withErrors(['submit' => 'Erro ao processar formulário. Tente novamente.'])
                ->withInput();
        }
    }
}
