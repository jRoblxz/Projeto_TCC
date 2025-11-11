<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peneiras;

class PeneiraController
{
    public function index()
    {
        // Lógica para listar as peneiras
        $peneiras = Peneiras::orderByDesc('data_evento');
        return view('peneiras.index', compact('peneiras'));
    }
    public function store(Request $request)
    {
        // Lógica para armazenar uma nova peneira
        $peneiras = Peneiras::create([
            'nome_evento' => $request->nome_evento,
            'data_evento' => $request->data_evento,
            'local' => $request->local,
            'descricao' => $request->descricao,
            'status' => $request->status,
            'sub_divisao' => $request->sub_divisao,
        ]);

        return view('peneiras.index', compact('peneiras'));
    }
    public function show($id)
    {
        // Lógica para mostrar detalhes de uma peneira específica
        $peneiras = Peneiras::findOrFail($id);
        return view('peneira.show', compact('peneiras'));
    }
    public function edit($id)
    {
        // Lógica para editar uma peneira específica
        $peneiras = Peneiras::findOrFail($id);
        return view('peneira.edit', compact('peneiras'));
    }
    public function update(Request $request, $id)
    {
        // Lógica para atualizar uma peneira específica
        $request->validate([
            'data_evento' => 'required|date',
            'local' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        $peneira = Peneiras::findOrFail($id);
        $peneira->update($request->all());

        return view('peneiras.index', compact('peneira'));

    }
    public function destroy($id)
    {
        // Lógica para deletar uma peneira específica
        $peneira = Peneiras::findOrFail($id);
        $peneira->delete();

        return redirect('peneiras.index')->with('success','Peneira Excluída com sucesso!');
    }
}
