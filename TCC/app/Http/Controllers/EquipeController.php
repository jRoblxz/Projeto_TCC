<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peneiras;
use App\Services\GeradorEquipeService;

class EquipeController
{
    protected $geradorEquipeService;

    public function __construct(GeradorEquipeService $geradorEquipeService)
    {
        $this->geradorEquipeService = $geradorEquipeService;
    }

    /**
     * Pega a requisição POST e dispara a lógica de montagem.
     */
    public function montarEquipes(Request $request, $id)
    {
        // 1. Encontra a peneira pelo ID que veio da URL
        $peneira = Peneiras::findOrFail($id);

        try {
            // 2. Chama o Service para fazer o trabalho pesado
            $this->geradorEquipeService->gerarEquipesParaPeneira($peneira);

            // 3. Se deu certo, redireciona de volta com msg de sucesso
            return redirect()->back()->with('success', 'Equipes geradas com sucesso!');
        } catch (\Exception $e) {
            // 4. Se deu erro (ex: falta de jogadores), volta com msg de erro
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
