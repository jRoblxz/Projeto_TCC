<?php

namespace App\Services;

use App\Models\Peneiras;

class PeneiraService
{
    public function getAll($perPage = 9, $filters = [])
    {
        $query = Peneiras::query();

        // 1. Filtro por Busca
        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function($q) use ($term) {
                $q->where('nome_evento', 'LIKE', "%{$term}%")
                  ->orWhere('local', 'LIKE', "%{$term}%");
            });
        }

        // 2. Filtro por Sub Divisão
        if (!empty($filters['sub_divisao']) && $filters['sub_divisao'] !== 'Todas') {
            $query->where('sub_divisao', 'LIKE', "%{$filters['sub_divisao']}%");
        }

        // 3. [NOVO] Filtro por Status
        if (!empty($filters['status']) && $filters['status'] !== 'Todas') {
            // O banco salva como 'AGENDADA', 'FINALIZADA', etc.
            $query->where('status', $filters['status']);
        }

        $query->orderBy('data_evento', 'desc');

        return $query->paginate($perPage);
    }

    public function create(array $data)
    {
        return Peneiras::create($data);
    }

    public function update($id, array $data)
    {
        $peneira = Peneiras::findOrFail($id);
        $peneira->update($data);
        return $peneira;
    }

    public function delete($id)
    {
        $peneira = Peneiras::findOrFail($id);
        
        // Regra de negócio: Não pode deletar peneira com inscritos?
        if ($peneira->inscricoes()->exists()) {
           throw new \Exception("Não é possível deletar uma peneira que já possui inscrições.");
        }

        $peneira->delete();
        return true;
    }
}