@extends('navbar')
@section('content')
    <div class="container">
        <div class="header-peneira">
            <h1>Gerenciamento de Peneiras</h1>
            <div class="header-actions">
                <div class="search-box">
                    <input type="text" placeholder="Buscar peneiras..." id="searchInput">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </div>
                <button class="btn-peneira btn-primary" onclick="abrirFormModal('nova')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nova Peneira
                </button>
            </div>
        </div>
        <div class="cards-grid-peneira" id="peneirasGrid">
            @if(isset($peneiras) && $peneiras->count() > 0)
                @foreach($peneiras as $peneira)
                    {{-- [CORREÇÃO] Adicionados atributos data-* para passar dados ao JS --}}
                    <div class="card-peneira" data-id="{{ $peneira->peneira_id ?? $peneira->id }}" {{-- Ajuste caso o nome da PK
                        seja 'id' --}} data-titulo="{{ $peneira->nome_evento }}"
                        data-data="{{ $peneira->data_evento ? \Carbon\Carbon::parse($peneira->data_evento)->format('Y-m-d\TH:i') : '' }}"
                        data-local="{{ $peneira->local }}" data-info="{{ $peneira->sub_divisao ?? 'Idade: 15-17 anos | 30 vagas' }}"
                        {{-- Use o campo correto do BD --}} data-status="{{ $peneira->status ?? 'aberta' }}"
                        data-descricao="{{ $peneira->descricao }}">

                        <div class="card-header-peneira">
                            <div>
                                <div class="card-title-peneira">{{ $peneira->nome_evento }}</div>
                            </div>
                            {{-- [CORREÇÃO] Status dinâmico --}}
                            <span class="card-status-peneira status-{{ $peneira->status ?? 'aberta' }}">
                                {{ ucfirst($peneira->status ?? 'Aberta') }}
                            </span>
                        </div>

                        <div class="card-info-peneira">
                            <div class="info-item">
                                <svg ...></svg>
                                {{-- [CORREÇÃO] Data dinâmica --}}
                                <span>
                                    @if($peneira->data_evento)
                                        {{ \Carbon\Carbon::parse($peneira->data_evento)->format('d \de M \de Y \à\s H:i') }}
                                    @else
                                        Data não definida
                                    @endif
                                </span>
                            </div>
                            <div class="info-item">
                                <svg ...></svg>
                                {{-- [CORREÇÃO] Local dinâmico --}}
                                <span>{{ $peneira->local ?? 'Local não definido' }}</span>
                            </div>
                            <div class="info-item">
                                <svg ...></svg>
                                {{-- [CORREÇÃO] Info dinâmica (ajuste os campos conforme seu BD) --}}
                                <span>{{ $peneira->sub_divisao ?? 'Idade: 15-17 anos | 30 vagas' }}</span>
                            </div>
                        </div>

                        <div class="card-description">
                            {{-- [CORREÇÃO] Descrição dinâmica --}}
                            {{ $peneira->descricao ?? 'Descrição não fornecida.' }}
                        </div>

                        <div class="card-peneira-actions">
                            {{-- [CORREÇÃO] Rota 'Visualizar' deve ser 'peneiras.show' --}}
                            <button class="btn-peneira btn-success btn-sm-peneira"
                                onclick="window.location.href='{{ route('peneiras.show', $peneira->peneira_id ?? $peneira->id) }}'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Visualizar
                            </button>

                            {{-- [CORREÇÃO] Passando 'this' (o botão) para a função JS --}}
                            <button class="btn-peneira btn-warning btn-sm-peneira" onclick="abrirFormModal('editar', this)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Editar
                            </button>

                            {{-- [CORREÇÃO] Passando 'this' (o botão) para a função JS --}}
                            <button class="btn-peneira btn-danger btn-sm" onclick="abrirExcluirModal(this)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Excluir
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-players">
                    {{-- [CORREÇÃO] Mensagem corrigida --}}
                    <p>Nenhuma peneira cadastrada ainda.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="modal" id="modal-form-peneira">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('modal-form-peneira')">&times;</button>
            <h3 id="modal-form-titulo">Nova Peneira</h3>

            <form id="peneira-form" method="POST" action="" onsubmit="event.preventDefault(); salvarPeneira();">
                @csrf
                <input type="hidden" id="peneira_id" name="peneira_id">

                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" id="titulo" name="nome_evento" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="data">Data e Hora</label>
                    <input type="datetime-local" id="data" name="data_evento" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="local">Local</label>
                    <input type="text" id="local" name="local" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="info">Informações (Idade, Vagas)</label>
                    <input type="text" id="info" name="sub_divisao" class="form-control"
                        placeholder="Ex: Idade: 15-17 anos | 30 vagas">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="AGENDADA">Agendada</option>
                        <option value="EM_ANDAMENTO">Em Andamento</option>
                        <option value="FINALIZADA">Finalizada</option>
                        <option value="CANCELADA">Cancelada</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4" class="form-control"></textarea>
                </div>

                <div style="text-align: right;">
                    <button type="submit" onclick="salvarPeneira()" class="btn-peneira btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="modal-excluir-peneira">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('modal-excluir-peneira')">&times;</button>
            <h3>Confirmar Exclusão</h3>
            <p>Você tem certeza que deseja excluir a peneira "<strong id="excluir-peneira-titulo"></strong>"?</p>
            <p>Esta ação não pode ser desfeita.</p>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <form id="form-excluir-peneira" method="POST" action="">
                    @csrf
                    @method('DELETE')

                    <input type="hidden" id="excluir-peneira-id">
                    <button class="btn-peneira btn-primary" type="button"
                        onclick="fecharModal('modal-excluir-peneira')">Cancelar</button>
                    <button class="btn-peneira btn-danger" type="button" onclick="confirmarExclusao()">Excluir</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Seletores dos elementos principais
        const formModal = document.getElementById('modal-form-peneira');
        const deleteModal = document.getElementById('modal-excluir-peneira');
        const peneiraForm = document.getElementById('peneira-form');
        const modalTitulo = document.getElementById('modal-form-titulo');

        // Formulário de exclusão
        const deleteForm = document.getElementById('form-excluir-peneira');

        // --- Funções de Controle do Modal ---

        function fecharModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // [CORRIGIDO] Função 'abrirFormModal'
        function abrirFormModal(modo, botao) {
            peneiraForm.reset(); // Limpa o formulário

            // Limpa o método @method('PUT') se ele existir
            const methodInput = peneiraForm.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }

            if (modo === 'nova') {
                modalTitulo.innerText = 'Nova Peneira';

                // Define a ação do formulário para 'store' (Criar)
                peneiraForm.action = "{{ route('peneiras.store') }}";

            }
            else if (modo === 'editar') {
                modalTitulo.innerText = 'Editar Peneira';

                // Encontra o 'card-peneira' pai do botão que foi clicado
                const card = botao.closest('.card-peneira');
                const peneiraId = card.dataset.id;

                // Define a ação do formulário para 'update' (Atualizar)
                // Precisamos construir a URL dinamicamente
                peneiraForm.action = `/peneiras/${peneiraId}`; // Ajuste se sua URL base for diferente

                // Adiciona o campo de método 'PUT' para o Laravel entender que é um update
                const putInput = document.createElement('input');
                putInput.type = 'hidden';
                putInput.name = '_method';
                putInput.value = 'PUT';
                peneiraForm.appendChild(putInput);

                // Preenche o formulário com dados dos atributos data-* do card
                // [NOTA] Certifique-se que os 'id' dos inputs batem com 'getElementById'
                document.getElementById('titulo').value = card.dataset.titulo;
                document.getElementById('data').value = card.dataset.data;
                document.getElementById('local').value = card.dataset.local;
                document.getElementById('info').value = card.dataset.info; // Assumindo que o ID é 'info'
                document.getElementById('status').value = card.dataset.status;
                document.getElementById('descricao').value = card.dataset.descricao;
            }

            formModal.classList.add('show');
        }

        // [CORRIGIDO] Função 'abrirExcluirModal'
        function abrirExcluirModal(botao) {
            const card = botao.closest('.card-peneira');
            const peneiraId = card.dataset.id;

            // Preenche com dados reais do card
            document.getElementById('excluir-peneira-titulo').innerText = card.dataset.titulo;

            // Define a ação do formulário de exclusão
            deleteForm.action = `/peneiras/${peneiraId}`; // Ajuste se sua URL base for diferente

            deleteModal.classList.add('show');
        }

        // --- Funções de Ação (REAIS) ---

        // Esta função agora é chamada pelo 'onclick' do botão Salvar no modal
        function salvarPeneira() {
            // Apenas submete o formulário. O 'action' e o 'method' já foram definidos
            // em 'abrirFormModal'
            peneiraForm.submit();
        }

        // Esta função agora é chamada pelo 'onclick' do botão Excluir no modal
        function confirmarExclusao() {
            // Apenas submete o formulário. O 'action' já foi definido
            // em 'abrirExcluirModal'
            deleteForm.submit();
        }

        // Adiciona listener para fechar modal clicando fora (no overlay)
        window.addEventListener('click', (event) => {
            if (event.target === formModal) {
                fecharModal('modal-form-peneira');
            }
            if (event.target === deleteModal) {
                fecharModal('modal-excluir-peneira');
            }
        });

    </script>
@endsection