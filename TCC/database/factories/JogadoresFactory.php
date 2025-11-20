<?php

namespace Database\Factories;

use App\Models\Jogadores;
use App\Models\Pessoas; // Importar o Model Pessoas
use Illuminate\Database\Eloquent\Factories\Factory;

class JogadoresFactory extends Factory
{
    protected $model = Jogadores::class;

    public function definition()
    {
        $posicoes = ['Goleiro', 'Zagueiro Direito', 'Lateral Esquerdo', 'Volante', 'Meia', 'Atacante'];
        $lesao = ['sim', 'não'];

        return [
            // FK: Cria uma nova pessoa (do tipo Jogador) e pega o ID dela
            'pessoa_id' => Pessoas::factory()->jogador(),

            'pe_preferido' => $this->faker->randomElement(['DIREITO', 'ESQUERDO']),
            'posicao_principal' => $this->faker->randomElement($posicoes),
            'posicao_secundaria' => $this->faker->randomElement($posicoes),
            'altura_cm' => $this->faker->numberBetween(160, 200),
            'peso_kg' => $this->faker->randomFloat(2, 55, 95),
            'historico_lesoes_cirurgias' => $this->faker->randomElement($lesao),
            'video_apresentacao_url' => $this->faker->url,
        ];
    }
}
