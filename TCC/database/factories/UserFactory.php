<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * O nome do Model correspondente.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Definição padrão do estado do Model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define o conjunto padrão de dados
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            // Criptografa a senha para 'password'
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            // O papel padrão será 'candidato'
            'role' => 'candidato',
        ];
    }

    /**
     * Indica que o usuário tem a função de 'admin'.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<User>
     */
    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
            ];
        });
    }

    /**
     * Indica que o usuário tem a função de 'treinador'.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<User>
     */
    public function treinador(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'treinador',
            ];
        });
    }

    /**
     * Indica que o usuário tem a função de 'candidato'.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<User>
     */
    public function candidato(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'candidato',
            ];
        });
    }
}
