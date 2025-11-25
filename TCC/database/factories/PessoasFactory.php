<?php

namespace Database\Factories;

use App\Models\Pessoas;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class PessoasFactory extends Factory
{
    protected $model = Pessoas::class;

    public function definition()
    {
        $urlFinal = null;

        // Tenta pegar arquivos locais APENAS se a pasta existir
        // Certifique-se que o disco 'local' aponta para storage/app
        if (Storage::disk('local')->exists('seeds/perfis')) {
            $arquivosLocais = Storage::disk('local')->files('seeds/perfis');

            if (!empty($arquivosLocais)) {
                $imagemEscolhida = fake()->randomElement($arquivosLocais);
                
                // Define o nome final do arquivo na nuvem
                $nomeArquivoNuvem = 'perfis/' . fake()->uuid() . '.jpg';

                // Envia para o Google
                Storage::disk('google')->put(
                    $nomeArquivoNuvem,
                    Storage::disk('local')->get($imagemEscolhida),
                    'public'
                );

                $urlFinal = Storage::disk('google')->url($nomeArquivoNuvem);
            }
        }

        return [
            'nome_completo' => fake('pt_BR')->name(),
            
            'data_nascimento' => fake()->dateTimeBetween('-20 years', '-7 years')->format('Y-m-d'),
            
            'cpf' => fake('pt_BR')->cpf(),
            
            'rg' => fake()->unique()->numerify('##.###.###-#'),
            
            'telefone' => fake('pt_BR')->cellphoneNumber(),
            
            'email' => fake()->unique()->safeEmail(),
            
            'senha' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            
            'foto_perfil_url' => $urlFinal, // <--- Agora usa a variável, seja ela null ou a URL
            
            'tipo_usuario' => 'C', // Define um padrão 'Comum' caso não seja especificado
            
            'cidade' => fake('pt_BR')->city(),
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