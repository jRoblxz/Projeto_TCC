<?php

use App\Models\Peneiras;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeneirasFactory extends Factory
{
    protected $model = Peneiras::class;

    public function definition()
    {
        $subDivisoes = ['Sub-7', 'Sub-9', 'Sub-11', 'Sub-13', 'Sub-15', 'Sub-17', 'Sub-20'];
        $status = ['AGENDADA', 'EM_ANDAMENTO', 'FINALIZADA', 'CANCELADA'];

        return [
            'nome_evento' => 'Peneira Oficial ' . $this->faker->randomElement($subDivisoes) . ' - ' . $this->faker->company,
            'data_evento' => $this->faker->dateTimeBetween('+1 week', '+6 months'),
            'local' => $this->faker->address,
            'descricao' => $this->faker->text(200),
            'status' => $this->faker->randomElement($status),
            'sub_divisao' => $this->faker->randomElement($subDivisoes),
        ];
    }
}
