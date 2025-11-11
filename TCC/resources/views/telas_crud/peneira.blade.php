@extends('navbar') @section('content')
    <div class="container">
        <div class="header-peneira">
            <h1>Gerenciamento de Peneiras</h1>
            <div class="header-actions">
                <div class="search-box">
                    <input type="text" placeholder="Buscar peneiras..." id="searchInput">
                    
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            
            @forelse($peneiras as $peneira)
                <div class="card-peneira">
                    <div class="card-header-peneira">
                        <div>
                            <div class="card-title-peneira">{{ $peneira->nome_evento }}</div>
                        </div>
                        <span class="card-status-peneira status-{{ $peneira->status }}">
                            {{ ucfirst($peneira->status) }} </span>
                    </div>
                    <div class="card-info-peneira">
                        <div class="info-item">
                            <svg viewBox="0 0 24 24" ...> </svg>
                            <span>
                                {{ $peneira->data_evento ? \Carbon\Carbon::parse($peneira->data_evento)->format('d/m/Y \à\s H:i') : 'Data não definida' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <svg viewBox="0 0 24 24" ...> </svg>
                            <span>{{ $peneira->local }}</span>
                        </div>
                        <div class="info-item">
                            <svg viewBox="0 0 24 24" ...> </svg>
                            <span>{{ $peneira->sub_divisao }}</span>
                        </div>
                    </div>
                    <div class="card-description">
                        {{ $peneira->descricao }}
                    </div>
                    
                    <div class="card-peneira-actions">
                        <a class="btn-peneira btn-success btn-sm-peneira" 
                           href="{{ route('peneira.show', ['id' => $peneira->id]) }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Visualizar
                        </a>
                        
                        <button class="btn-peneira btn-warning btn-sm-peneira" onclick="abrirFormModal('editar')">
                            <svg width="16" height="16" viewBox="0 0 24 24" ...> </svg>
                            Editar
                        </button>
                        <button class="btn-peneira btn-danger btn-sm" onclick="abrirExcluirModal()">
                            <svg width="16" height="16" viewBox="0 0 24 24" ...> </svg>
                            Excluir
                        </button>
                    </div>
                </div>
            @empty
                <div class="card-peneira" style="text-align: center; padding: 20px;">
                    <p>Nenhuma peneira cadastrada no momento.</p>
                    <p>Clique em "Nova Peneira" para começar.</p>
                </div>
            @endforelse
            
        </div>
        </div>

    
    <div class="modal" id="modal-form-peneira">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('modal-form-peneira')">&times;</button>
            <h3 id="modal-form-titulo">Nova Peneira</h3>
            
            <form id="peneira-form" onsubmit="event.preventDefault(); salvarPeneira();">
                <input type="hidden" id="peneira_id" name="peneira_id">
                
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" id="titulo" name="titulo" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="data">Data e Hora</label>
                    <input type="datetime-local" id="data" name="data" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="local">Local</label>
                    <input type="text" id="local" name="local" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="info">Informações (Idade, Vagas)</label>
                    <input type="text" id="info" name="info" class="form-control" placeholder="Ex: Idade: 15-17 anos | 30 vagas">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="aberta">Aberta</option>
                        <option value="agendada">Agendada</option>
                        <option value="encerrada">Encerrada</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4" class="form-control"></textarea>
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn-peneira btn-primary">Salvar</button>
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
                <input type="hidden" id="excluir-peneira-id">
                <button class="btn-peneira btn-primary" onclick="fecharModal('modal-excluir-peneira')">Cancelar</button>
                
                <button class="btn-peneira btn-danger" onclick="confirmarExclusao()">Excluir</button>
            </div>
        </div>
    </div>

<script>
    // Seletores dos elementos principais
    const formModal = document.getElementById('modal-form-peneira');
    const deleteModal = document.getElementById('modal-excluir-peneira');
    const peneiraForm = document.getElementById('peneira-form');
    const modalTitulo = document.getElementById('modal-form-titulo');

    // --- Funções de Controle do Modal ---

    function fecharModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    // Função que abre o modal, limpa o form e preenche (se for edição)
    function abrirFormModal(modo) {
        peneiraForm.reset(); // Limpa o formulário
        document.getElementById('peneira_id').value = ''; // Limpa o ID

        if (modo === 'nova') {
            modalTitulo.innerText = 'Nova Peneira';
            // O formulário está limpo, pronto para preenchimento
        } 
        else if (modo === 'editar') {
            modalTitulo.innerText = 'Editar Peneira (Estático)';
            
            // Preenche o formulário com dados FALSOS (hardcoded)
            // TODO: Depois temos que fazer um fetch/AJAX pra buscar os dados reais
            document.getElementById('peneira_id').value = '999'; // ID falso
            document.getElementById('titulo').value = 'Peneira de Edição Estática';
            document.getElementById('data').value = '2025-12-01T10:00'; // Data falsa
            document.getElementById('local').value = 'CT Falso de Testes';
            document.getElementById('info').value = 'Idade: 18-20 | 10 vagas';
            document.getElementById('status').value = 'agendada';
            document.getElementById('descricao').value = 'Esta é uma descrição estática vinda do JavaScript para simular a edição.';
        }

        formModal.classList.add('show');
    }

    // [ALTERADO] A função agora usa dados falsos
    function abrirExcluirModal() {
        // Preenche com dados FALSOS (hardcoded)
        // TODO: Buscar o ID e o Título real do card que foi clicado
        document.getElementById('excluir-peneira-titulo').innerText = 'Peneira Falsa (ID: 999)';
        document.getElementById('excluir-peneira-id').value = '999';

        deleteModal.classList.add('show');
    }

    // --- Funções de Ação (Simuladas) ---
    // (Não precisam de alteração, apenas simulam o clique)

    function salvarPeneira() {
        const id = document.getElementById('peneira_id').value;
        if (id) {
            console.log('Salvando (Update) Peneira ID:', id);
        } else {
            console.log('Salvando (Create) Nova Peneira');
        }
        
        // TODO: Substituir esse alert por um submit de form real
        // ou uma chamada AJAX.
        alert('Peneira salva com sucesso! (simulação)');
        fecharModal('modal-form-peneira');
    }

    function confirmarExclusao() {
        const id = document.getElementById('excluir-peneira-id').value;
        console.log('Excluindo Peneira ID:', id);

        // TODO: Fazer o AJAX de DELETE aqui...
        alert('Peneira excluída com sucesso! (simulação)');
        fecharModal('modal-excluir-peneira');
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