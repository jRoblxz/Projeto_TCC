<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->pessoa->nome_completo ?? 'N/A',
            'email' => $this->pessoa->email ?? 'N/A',
            'photo_url' => $this->pessoa->foto_perfil_url 
                ? 'https://storage.googleapis.com/'.env('GOOGLE_CLOUD_STORAGE_BUCKET').'/'.$this->pessoa->foto_perfil_url 
                : null,
            'position' => $this->posicao_principal,
            'secondary_position' => $this->posicao_secundaria,
            'height' => $this->altura_cm,
            'weight' => $this->peso_kg,
            'rating' => $this->rating_medio, // Accessor
            'subdivision' => $this->pessoa->sub_divisao, // Accessor logic
        ];
    }
}