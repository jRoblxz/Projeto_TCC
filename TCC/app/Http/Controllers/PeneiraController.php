<?php

namespace App\Http\Controllers;

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
class PeneiraController
{
    public function index()
    {
        // Lógica para listar as peneiras
        // [CORREÇÃO] Adicionado ->get() para executar a consulta
        $peneiras = Peneiras::orderByDesc('data_evento')->get(); 

        $jogadores = Jogadores::with('pessoa')->orderByDesc('id')->get();
        
        // [NOTA] Certifique-se que este view path está correto.
        // Se seu arquivo está em /resources/views/telas_crud/peneira.blade.php
        // você deve usar: return view('telas_crud.peneira', compact('peneiras'));
        return view('telas_crud.peneira', [
            'peneiras' => $peneiras,
            'jogadores' => $jogadores
        ]);
    }
    
    public function store(Request $request)
    {
        // Lógica para armazenar uma nova peneira
        // [CORREÇÃO] O store deve criar o item e REDIRECIONAR de volta para o index
        Peneiras::create([
            'nome_evento' => $request->nome_evento,
            'data_evento' => $request->data_evento,
            'local' => $request->local,
            'descricao' => $request->descricao,
            'status' => $request->status,
            'sub_divisao' => $request->sub_divisao,
        ]);

        return redirect()->route('peneiras.index')->with('success','Peneira criada com sucesso!');
    }
    
    public function show($id)
    {
        // 1. Lógica para mostrar detalhes de uma peneira específica (você já tinha)
        $peneiras = Peneiras::findOrFail($id);
        
        // 2. [CORREÇÃO] Adicione esta linha para buscar os jogadores
        $jogadores = Jogadores::with('pessoa')->orderByDesc('id')->get();

        // 3. [CORREÇÃO] Altere o return para enviar AMBAS as variáveis
        return view('telas_crud.peneira_id', [
            'peneiras' => $peneiras,
            'jogadores' => $jogadores
        ]);

        /* // O seu código antigo estava assim e só enviava 'peneiras':
        return view('telas_crud.peneira_id', compact('peneiras'));
        */
    }
    
    public function edit($id)
    {
        // Lógica para editar uma peneira específica
        $peneiras = Peneiras::findOrFail($id);
        
        // [NOTA] Você precisa criar este arquivo de view: /resources/views/peneiras/edit.blade.php
        return view('peneiras.edit', compact('peneiras'));
    }
    
    public function update(Request $request, $id)
    {
        // Lógica para atualizar uma peneira específica
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

        // [CORREÇÃO] Deve redirecionar após o update
        return redirect()->route('peneiras.index')->with('success','Peneira atualizada com sucesso!');
    }
    
    public function destroy($id)
    {
        // Lógica para deletar uma peneira específica
        $peneira = Peneiras::findOrFail($id);
        $peneira->delete();

        // [CORREÇÃO] O redirecionamento deve usar ->route()
        return redirect()->route('peneiras.index')->with('success','Peneira Excluída com sucesso!');
    }
}