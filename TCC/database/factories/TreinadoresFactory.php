<?php

use App\Models\Treinadores;
use App\Models\Pessoas;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreinadoresFactory extends Factory
{
    protected $model = Treinadores::class;

    public function definition()
    {
        return [
            // FK: Cria uma nova pessoa (do tipo Treinador) e pega o ID dela
            'pessoa_id' => Pessoas::factory()->treinador(),

            'clube_organizacao' => $this->faker->company,
            'cargo' => $this->faker->jobTitle,
            'cref' => $this->faker->numerify('######-G/SP'),
            'biografia_resumo' => $this->faker->paragraph(2),
            'anos_experiencia' => $this->faker->numberBetween(1, 20),
        ];
    }
}
