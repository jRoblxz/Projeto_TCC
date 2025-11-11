@extends('navbar')
@section('content')
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
            
            <div class="card-peneira">
                <div class="card-header-peneira">
                    <div>
                        <div class="card-title-peneira">Peneira Sub-17 São Paulo FC</div>
                    </div>
                    <span class="card-status-peneira status-aberta">Aberta</span>
                </div>
                <div class="card-info-peneira">
                    <div class="info-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>15 de Dezembro de 2025 às 14:00</span>
                    </div>
                    <div class="info-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span>CT Barra Funda - São Paulo, SP</span>
                    </div>
                    <div class="info-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Idade: 15-17 anos | 30 vagas</span>
                    </div>
                </div>
                <div class="card-description">
                    Avaliação técnica para a categoria Sub-17. Testes físicos, fundamentos e jogo coletivo. Traga documento com foto e atestado médico.
                </div>
                
                <div class="card-peneira-actions">
                    <button class="btn-peneira btn-success btn-sm-peneira" onclick="window.location.href='{{ route('peneira.index') }}'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        Visualizar
                    </button>
                    <button class="btn-peneira btn-warning btn-sm-peneira" onclick="abrirFormModal('editar')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Editar
                    </button>
                    <button class="btn-peneira btn-danger btn-sm" onclick="abrirExcluirModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        Excluir
                    </button>
                </div>
            </div>
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

    // [ALTERADO] A função agora usa dados falsos para 'editar'
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
        
        alert('Peneira salva com sucesso! (simulação)');
        fecharModal('modal-form-peneira');
    }

    function confirmarExclusao() {
        const id = document.getElementById('excluir-peneira-id').value;
        console.log('Excluindo Peneira ID:', id);

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