<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PeneiraResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->nome_evento, // No React você pode usar 'title' se preferir
            'date' => $this->data_evento,
            'location' => $this->local,
            'status' => $this->status, // AGENDADA, EM_ANDAMENTO, FINALIZADA
            'subdivision' => $this->sub_divisao,
            'description' => $this->descricao,
            // Se precisar saber quantos inscritos tem para mostrar no card:
            'candidates_count' => $this->inscricoes_count ?? 0, 
        ];
    }
}