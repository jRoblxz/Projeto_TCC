<?php

use App\Models\Inscricoes;
use App\Models\Jogadores; // FK
use App\Models\Peneiras; // FK
use Illuminate\Database\Eloquent\Factories\Factory;

class InscricoesFactory extends Factory
{
    protected $model = Inscricoes::class;

    public function definition()
    {
        $status = ['PENDENTE', 'CONFIRMADA', 'RECUSADA', 'LISTA_ESPERA'];

        return [
            // FK: Cria um novo Jogador (que por sua vez cria uma Pessoa)
            'jogador_id' => Jogadores::factory(),

            // FK: Cria uma nova Peneira
            'peneira_id' => Peneiras::factory(),

            'data_inscricao' => $this->faker->dateTimeThisYear,
            'status' => $this->faker->randomElement($status),
            // Nota: Você pode precisar ajustar a lógica para garantir a 'UNIQUE KEY (jogador_id,peneira_id)'
        ];
    }
}
