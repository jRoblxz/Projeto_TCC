<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAllUsers(array $filters)
    {
        $query = User::with(['pessoa.jogador', 'pessoa.treinador']);

        // Filtro por Cargo
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Filtro de Busca
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('pessoa', function($pq) use ($search) {
                      $pq->where('nome_completo', 'like', "%{$search}%")
                         ->orWhere('cpf', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro de Idade (Jogadores)
        if (!empty($filters['role']) && $filters['role'] === 'candidato' && !empty($filters['subdivisao'])) {
            $idades = match ($filters['subdivisao']) {
                'Sub-7'  => [6, 7], 'Sub-9'  => [8, 9], 'Sub-11' => [10, 11],
                'Sub-13' => [12, 13], 'Sub-15' => [14, 15], 'Sub-17' => [16, 17],
                'Sub-20' => [18, 21], default  => null
            };

            if ($idades) {
                $dataInicio = now()->subYears($idades[1] + 1)->format('Y-m-d');
                $dataFim    = now()->subYears($idades[0])->format('Y-m-d');
                $query->whereHas('pessoa', function($q) use ($dataInicio, $dataFim) {
                    $q->whereBetween('data_nascimento', [$dataInicio, $dataFim]);
                });
            }
        }

        // ==========================================
        // NOVA LÓGICA DE ORDENAÇÃO
        // ==========================================
        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        
        // Bloqueio de segurança: só permite ordenar por estas colunas
        $allowedSorts = ['name', 'email', 'created_at', 'role'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir);
        }

        $perPage = $filters['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    // Buscar um usuário específico para preencher o formulário
    public function getUserById($id)
    {
        return User::with(['pessoa.treinador'])->findOrFail($id);
    }

    // Atualizar os dados do usuário
    public function updateUser($id, array $data, $photo = null)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data, $photo) {
            $user = User::findOrFail($id);
            $pessoa = $user->pessoa;

            // 1. Atualiza a Foto (Se enviou uma nova)
            if ($photo) {
                // Aqui usa o seu FileService que já existe no Cadastro
                $path = app(FileService::class)->upload($photo, 'user');
                $pessoa->foto_perfil_url = $path;
            }

            // 2. Atualiza Pessoa e User
            $pessoa->nome_completo = $data['name'];
            $pessoa->email = $data['email'];
            $pessoa->save();

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->role = $data['role'];

            // Só altera a senha se o usuário digitou uma nova
            if (!empty($data['password'])) {
                $user->password = \Illuminate\Support\Facades\Hash::make($data['password']);
            }
            $user->save();

            // 3. Atualiza dados específicos de Treinador
            if ($data['role'] === 'treinador') {
                $pessoa->treinador()->updateOrCreate(
                    ['pessoa_id' => $pessoa->id],
                    [
                        'clube_organizacao' => $data['clube_organizacao'] ?? null,
                        'cargo' => $data['cargo'] ?? null,
                        'cref' => $data['cref'] ?? null,
                        'anos_experiencia' => $data['anos_experiencia'] ?? null,
                        'biografia_resumo' => $data['biografia_resumo'] ?? null,
                    ]
                );
            } else {
                // Se mudou de treinador para adm, podemos apagar o registro de treinador
                $pessoa->treinador()->delete();
            }

            return $user;
        });
    }

    public function getMapStats()
    {
        // 1. Busca todos os usuários 'candidato' e já traz as informações de 'pessoa'
        $candidatos = \App\Models\User::with('pessoa')
            ->where('role', 'candidato')
            ->get();

        // 2. Usa as Collections do Laravel para Filtrar, Agrupar e Contar (Muito mais seguro)
        $dadosMapa = $candidatos
            ->filter(function ($user) {
                // Deixa passar apenas quem tem o perfil preenchido e possui coordenadas
                return $user->pessoa && $user->pessoa->latitude && $user->pessoa->longitude;
            })
            ->groupBy(function ($user) {
                // Cria um grupo único para cada coordenada exata
                return $user->pessoa->latitude . '|' . $user->pessoa->longitude;
            })
            ->map(function ($grupo) {
                // Pega os dados da cidade (baseado no primeiro jogador do grupo)
                $pessoa = $grupo->first()->pessoa;
                
                return [
                    'cidade' => $pessoa->cidade ?? 'Desconhecida',
                    'latitude' => (float) $pessoa->latitude,
                    'longitude' => (float) $pessoa->longitude,
                    'total' => $grupo->count() // Conta automaticamente quantos caíram neste grupo
                ];
            });

        // 3. Retorna os valores limpos como um Array para o React
        return $dadosMapa->values()->toArray();
    }
}