<?php

namespace Database\Factories;

use App\Models\Avaliacao;
use App\Models\Jogadores;
use App\Models\Treinadores;
use App\Models\Peneiras;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvaliacaoFactory extends Factory
{
    /**
     * O nome do Model correspondente.
     *
     * @var string
     */
    protected $model = Avaliacao::class;

    /**
     * Definição padrão do estado do Model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define o intervalo de notas, por exemplo, de 0.0 a 10.0
        $nota = $this->faker->randomFloat(1, 0, 10);

        return [
            // Garante que o jogador_id seja um ID válido da tabela Jogadores
            'jogador_id' => Jogadores::all()->random()->id,

            // Garante que o treinador_id seja um ID válido da tabela Treinadores
            'treinador_id' => Treinadores::all()->random()->id,

            // Garante que o peneira_id seja um ID válido da tabela Peneiras
            'peneira_id' => Peneiras::all()->random()->id,

            'nota' => $nota,

            'observacoes' => $this->faker->paragraph(2), // Gera um parágrafo de 2 sentenças como observação

            'data_avaliacao' => $this->faker->dateTimeBetween('-1 year', 'now'), // Data de avaliação recente
        ];
    }
}
