<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Avaliacao;
use App\Models\Peneiras;
use App\Models\Jogadores;
use App\Models\Treinadores;
use App\Models\Inscricoes;
use App\Models\Equipes;

class FakeBd extends Seeder
{
    public function run()
    {
        // 1. Tabela PAI (Sem dependências)
        // Cria 10 eventos de peneira
        $peneiras = Peneiras::factory(10)->create();

        // 2. Cria Pessoas, Jogadores e Treinadores (Dependem apenas de Pessoas, que é criada na própria Factory)
        // Cria 50 Jogadores (e suas 50 Pessoas associadas)
        $jogadores = Jogadores::factory(50)->create();

        // Cria 5 Treinadores (e suas 5 Pessoas associadas)
        $treinadores = Treinadores::factory(5)->create();

        // 3. Tabela de Relacionamento (Dependências de Peneiras, Jogadores, Treinadores)

        // Cria 100 Inscrições
        Inscricoes::factory(100)->create();


        // Cria 50 Avaliações
        Avaliacao::factory(50)->create([
            'jogador_id' => $jogadores->random()->id,
            'treinador_id' => $treinadores->random()->id,
            'peneira_id' => $peneiras->random()->id,
        ]);
    }
}
