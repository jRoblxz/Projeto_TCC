<?php

// database/factories/PessoasFactory.php
namespace Database\Factories;

use App\Models\Pessoas;
use Illuminate\Database\Eloquent\Factories\Factory;

class PessoasFactory extends Factory
{
    protected $model = Pessoas::class;

    public function definition()
    {
        return [
            'nome_completo' => $this->faker->name,
            'data_nascimento' => $this->faker->date('Y-m-d', '-18 years'),
            'cpf' => $this->faker->unique()->numerify('###.###.###-##'),
            'rg' => $this->faker->unique()->numerify('##.###.###-#'),
            'telefone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'senha' => bcrypt('password'), // Senha padrão para login
            'foto_perfil_url' => null,
            'tipo_usuario' => '', // Valor padrão será sobrescrito pelos 'states'
            'cidade' => $this->faker->city,
        ];
    }

    // State para criar uma Pessoa que é Jogador
    public function jogador()
    {
        return $this->state(fn(array $attributes) => [
            'tipo_usuario' => 'J',
        ]);
    }

    // State para criar uma Pessoa que é Treinador
    public function treinador()
    {
        return $this->state(fn(array $attributes) => [
            'tipo_usuario' => 'T',
        ]);
    }
}
