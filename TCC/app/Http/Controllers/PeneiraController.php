<?php

namespace App\Http\Controllers;

use App\Models\Peneiras; 
use Illuminate\Http\Request;

class PeneiraController extends Controller
{
    /**
     * [MÉTODO INDEX]
     * Mostra a lista de todas as peneiras (peneira.blade.php).
     */
    public function index()
    {
        // 1. Busca TODAS as peneiras do banco de dados
        $peneiras = Peneiras::orderBy('data_evento', 'desc')->get();
        
        // 2. Manda os dados para a view da lista
        return view('peneira', ['peneiras' => $peneiras]);
    }


    public function show($id)
    {
        // 1. Busca UMA peneira pelo ID que veio da URL
        $peneira = Peneiras::findOrFail($id);
        
        // 2. Manda os dados para a NOVA view de detalhes
        return view('peneira-detalhes', ['peneira' => $peneira]);
    }

}