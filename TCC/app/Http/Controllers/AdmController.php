<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;
use App\Models\Avaliacao;
use App\Models\Views\DadosJogador;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PDO;
use Illuminate\Support\Facades\Storage;

class AdmController
{
    //tela/rota  da view Players
    public function index()
    {

        $jogadores = Jogadores::with('pessoa')->orderByDesc('id')->get();

        return view('telas_crud.players', ['jogadores' => $jogadores]);
    }

    //tela/rota  da view Home
    public function homepage(Request $request)
    {
        $query = Jogadores::with('pessoa')->orderByDesc('id');

        // Filtro por subdivisão
        if ($request->filled('subdivisao')) {
            $query->whereHas('pessoa', function ($q) use ($request) {
                $q->where('sub_divisao', $request->subdivisao);
            });
        }

        $jogadores = $query->get();
        $totalJogadores = $query->count(); // conta já filtrado

        return view('home', [
            'jogadores' => $jogadores,
            'totalJogadores' => $totalJogadores
        ]);
    }

    //tela/rota da view Player_info
    public function show(Jogadores $jogadores)
    {
        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);
        return view('telas_crud.player_info', ['jogador' => $jogador]);
    }
    //tela/rota da view Player_edit
    public function edit(Jogadores $jogadores)
    {

        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);
        return view('telas_crud.player_edit', ['jogador' => $jogador]);
    }

    //Update da crud
    public function update(Request $request, Jogadores $jogadores)
    {

        $request->validate([
            'nome_completo' => 'required|string|max:255',
            'altura_cm' => 'nullable|numeric',
            'peso_kg' => 'nullable|numeric',
            'pe_preferido' => 'nullable|string',
            'posicao_principal' => 'nullable|string',
            'posicao_secundaria' => 'nullable|string',
            //  'historico_lesoes_cirurgias' => 'nullable|string',
            //  'video_apresentacao_url' => 'nullable|url',
            // 'foto_perfil_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Buscar o jogador com pessoa
        $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);

        // Upload da imagem se enviada
        $fotoPath = $jogador->pessoa->foto_perfil_url; // Manter a atual
        if ($request->hasFile('image')) {
            // Deletar imagem antiga se existir
            if ($fotoPath && Storage::exists('public/' . $fotoPath)) {
                Storage::delete('public/' . $fotoPath);
            }

            // Salvar nova imagem
            $fotoPath = $request->file('image')->store('user', 'public');
        }

        // Atualizar dados da pessoa
        $jogador->pessoa->update([
            'nome_completo' => $request->nome_completo,
            // 'data_nascimento' => $request->data_nascimento,
            //   'cpf' => $request->cpf,
            //  'rg' => $request->rg,
            //  'email' => $request->email,
            //   'telefone' => $request->telefone,
            'foto_perfil_url' => $fotoPath,
        ]);

        $jogador->avaliacoes()->update([
            'nota' => $request->nota,
            'observacoes' => $request->observacoes,
        ]);

        // Atualizar dados do jogador
        $jogador->update([
            'pe_preferido' => $request->pe_preferido,
            'posicao_principal' => $request->posicao_principal,
            'posicao_secundaria' => $request->posicao_secundaria,
            // 'historico_lesoes_cirurgias' => $request->historico_lesoes_cirurgias,
            'altura_cm' => $request->altura_cm,
            'peso_kg' => $request->peso_kg,
            //  'video_apresentacao_url' => $request->video_apresentacao_url,
        ]);

        return redirect()->route('jogadores.info', ['jogadores' => $jogador->id])
            ->with('success', 'Jogador atualizado com sucesso!');;
    }

    //Delete da crud
    public function destroy(Jogadores $jogadores)
    {
        try {
            // O jogador já é injetado pela rota
            $jogador = Jogadores::with('pessoa')->findOrFail($jogadores->id);

            // Deletar foto se existir
            if ($jogador->pessoa->foto_perfil_url && Storage::exists('public/' . $jogador->pessoa->foto_perfil_url)) {
                Storage::delete('public/' . $jogador->pessoa->foto_perfil_url);
            }

            // Deletar avaliações relacionadas primeiro
            Avaliacao::where('jogador_id', $jogador->id)->delete();

            // Salvar pessoa para deletar depois
            $pessoa = $jogador->pessoa;

            // Deletar o jogador primeiro
           // $jogador->delete();

            // Deletar a pessoa
            $jogador->pessoa->delete();

            return redirect()->route('jogadores.index')
                ->with('success', 'Jogador deletado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao deletar jogador: ' . $e->getMessage());
        }
    }
}
