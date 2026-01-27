<?php

namespace App\Services;

use App\Models\Peneiras;
use Illuminate\Support\Facades\DB;
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
        return DB::transaction(function () use ($id) {
            $peneira = Peneiras::findOrFail($id);

            // 1. Limpeza de Equipes e Vínculos
            $equipeIds = DB::table('Equipes')->where('peneira_id', $id)->pluck('id');
            if ($equipeIds->count() > 0) {
                DB::table('JogadoresPorEquipe')->whereIn('equipe_id', $equipeIds)->delete();
                DB::table('Equipes')->where('peneira_id', $id)->delete();
            }

            // 2. Apagar Avaliações e Inscrições
            DB::table('Avaliacoes')->where('peneira_id', $id)->delete();
            DB::table('Inscricoes')->where('peneira_id', $id)->delete();

            // 3. Apagar Peneira
            $peneira->delete();
            
            return true;
        });
    }
}