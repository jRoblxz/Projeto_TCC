<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Importar o Model User
use App\Models\Peneiras;
use App\Models\Jogadores;
use App\Models\Treinadores;
use App\Models\Inscricoes;
use App\Models\Avaliacao; // Assumindo Avaliacao (singular)
use App\Models\Equipes;

class FakeBd extends Seeder
{
    public function run()
    {
        // 0.1. Usuários Especiais (Com login fácil)
        // User::factory()->admin()->create([
        //     'name' => 'Admin Teste',
        //     'email' => 'admin@teste.com',
        // ]);

        // 0.2. População Geral de Usuários
        // Cria 5 usuários com o papel 'treinador'
        User::factory(5)->treinador()->create();
        // Cria 100 usuários com o papel 'candidato' (padrão)
        User::factory(50)->candidato()->create();

        // Cria 10 eventos de peneira
        $peneiras = Peneiras::factory(10)->create();

        // Cria 50 Jogadores (e suas 50 Pessoas associadas)
        $jogadores = Jogadores::factory(30)->create();

        // Cria 5 Treinadores (e suas 5 Pessoas associadas)
        $treinadores = Treinadores::factory(5)->create();

        $data = [];
        $existingCombinations = collect();
        $maxAttempts = 300; // Limite de tentativas para garantir que o Seeder não trave
        for ($i = 0; $i < 100; $i++) {
            $attempts = 0;
            do {
                $jogadorId = $jogadores->random()->id;
                $peneiraId = $peneiras->random()->id;
                $combination = "{$jogadorId}-{$peneiraId}";
                $attempts++;

                // Se a combinação não existe E não excedeu o limite de tentativas
            } while ($existingCombinations->contains($combination) && $attempts < $maxAttempts);

            if ($attempts >= $maxAttempts) {
                // Se travou, significa que quase todas as combinações já foram usadas
                break;
            }

            $existingCombinations->push($combination);

            $data[] = [
                'jogador_id' => $jogadorId,
                'peneira_id' => $peneiraId,
                'data_inscricao' => now(),
                'status' => ['PENDENTE', 'CONFIRMADA'][array_rand(['PENDENTE', 'CONFIRMADA'])],
            ];
        }

        Inscricoes::insert($data);

        Avaliacao::factory(25)->create();
    }
}
