<?php

namespace Database\Factories;

use App\Models\Inscricoes;
use App\Models\Jogadores; // FK
use App\Models\Peneiras; // FK
use Illuminate\Database\Eloquent\Factories\Factory;

class InscricoesFactory extends Factory
{
    protected $model = Inscricoes::class;

    public function definition()
    {
        $status = ['PENDENTE', 'CONFIRMADA', 'RECUSADA',];

        return [
            // FK: Cria um novo Jogador (que por sua vez cria uma Pessoa)
            'jogador_id' => Jogadores::all()->random()->id,

            // FK: Cria uma nova Peneira
            'peneira_id' => Peneiras::all()->random()->id,

            'data_inscricao' => $this->faker->dateTimeThisYear,
            'status' => $this->faker->randomElement($status),

        ];
    }
}
