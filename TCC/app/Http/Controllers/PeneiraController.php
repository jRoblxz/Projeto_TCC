<?php

namespace App\Http\Controllers;

// [FIX 1] Importe a classe base do Controller
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Peneiras;
use App\Http\Requests\UserRequest;
use App\Models\Jogadores;
use App\Models\Pessoas;
use App\Models\Avaliacao;
use App\Models\Views\DadosJogador;
use Illuminate\Support\Facades\DB;
use PDO;
use Illuminate\Support\Facades\Storage;

// [FIX 1] Faça a classe estender o BaseController
class PeneiraController extends BaseController
{
    public function index()
    {
        $peneiras = Peneiras::orderByDesc('data_evento')->get();

        // Esta linha está correta, aponta para seu peneira.blade.php
        return view('telas_crud.peneira', compact('peneiras'));
    }

    public function store(Request $request)
    {
        // Seu 'store' está correto
        Peneiras::create($request->all());
        return redirect()->route('peneiras.index')->with('success', 'Peneira criada com sucesso!');
    }

    public function show($id)
    {
        // 1. Busca a peneira específica (você já tinha)
        $peneiras = Peneiras::findOrFail($id);

        // 2. [NOVO] Busca os jogadores (a mesma lógica que usamos antes)
        $jogadores = Jogadores::with('pessoa')
            ->join('inscricoes', 'jogadores.id', '=', 'inscricoes.jogador_id')
            ->where('inscricoes.peneira_id', $id)
            ->select('jogadores.*') // Importante: seleciona apenas dados do jogador para não dar conflito de ID
            ->orderByDesc('jogadores.id')
            ->get();

        // 3. [CORRIGIDO] Envia AMBAS as variáveis para a view
        return view('telas_crud.peneira_id', [
            'peneiras' => $peneiras,
            'jogadores' => $jogadores
        ]);
    }

    public function edit($id)
    {
        // Como você disse, seu fluxo de 'Editar' é por modal 
        // e não usa uma página 'edit' separada.
        // O seu modal (em peneira.blade.php) submete direto para o 'update'.
        // Este método 'edit()' nunca será chamado pelo seu modal.
        // Vamos apenas redirecionar para a lista principal caso
        // alguém tente acessar a URL /peneiras/{id}/edit manualmente.
        return redirect()->route('peneiras.index');
    }

    public function update(Request $request, $id)
    {
        // Seu 'update' está correto e é o que o modal usa
        $request->validate([
            'nome_evento' => 'required|string|max:255',
            'data_evento' => 'required|date',
            'local' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'sub_divisao' => 'nullable|string|max:100',
            'descricao' => 'nullable|string',
        ]);

        $peneira = Peneiras::findOrFail($id);
        $peneira->update($request->all());

        return redirect()->route('peneiras.index')->with('success', 'Peneira atualizada com sucesso!');
    }

    public function destroy($id)
    {
        // Seu 'destroy' está correto
        $peneira = Peneiras::findOrFail($id);
        $peneira->delete();

        return redirect()->route('peneiras.index')->with('success', 'Peneira Excluída com sucesso!');
    }

    public function montarindex($id)
    {
        $peneira = Peneiras::findOrFail($id);
        return view('peneira-detalhes', $peneira);
    }
}
