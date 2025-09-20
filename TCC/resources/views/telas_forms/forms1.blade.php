@extends('telas_forms.header')
@section('content')
<div class="container-form">
  <!-- Passos -->
  <div class="steps">
    <div id="step-1" class="active">1. Informações básicas</div>
    <div id="step-2">2. Informações jogador</div>
    <div id="step-3">3. Vídeo apresentação</div>
  </div>

  <!-- Formulário -->
  <form id="signupForm">
    <!-- Etapa 1 -->
    <div class="form-step active">
      <div class="row inline-row mb-3">
        <div class="col-md-8">
          <label for="nome" class="form-label">Nome completo:</label>
          <input type="text" id="nome" name="nome" class="form-control" step="any" required="" placeholder="Informe seu nome">
        </div>
        <div class="col-md-2">
          <label for="idade" class="form-label">Idade:</label>
          <input type="number" id="idade" name="idade" class="form-control" step="any" required="" placeholder="Idade">
        </div>
      </div>
      <div class="row inline-row mb-3">
        <div class="col-md-3">
          <label for="nasc" class="form-label">Data Nascimento:</label>
          <input type="date" id="nasc" name="nasc" class="form-control" step="any" required="">
        </div>
        <div class="col-md-6">
          <label for="cidade" class="form-label">Cidade:</label>
          <input type="text" id="cidade" name="cidade" class="form-control" step="any" required="" placeholder="Informe sua cidade">
        </div>
      </div>
      <div class="row inline-row mb-3">
        <div class="col-md-5">
          <label for="cpf" class="form-label">CPF:</label>
          <input type="number" id="cpf" name="cpf" class="form-control" step="any" required="" placeholder="Informe seu CPF (apenas números)">
        </div>
        <div class="col-md-4">
          <label for="rg" class="form-label">RG:</label>
          <input type="number" id="rg" name="rg" class="form-control" step="any" required="" placeholder="Informe seu RG (apenas números">
        </div>
      </div>
      <div class="row inline-row mb-3">
        <div class="col-md-6">
          <label for="email" class="form-label">E-mail:</label>
          <input type="email" id="email" name="email" class="form-control" step="any" required="" placeholder="Informe seu e-mail">
        </div>
        <div class="col-md-6">
          <label for="fone" class="form-label">Celular:</label>
          <input type="number" id="fone" name="fone" class="form-control" step="any" required="" placeholder="Informe seu contato">
        </div>
      </div>
    </div>

    <!-- Etapa 2 -->
    <div class="form-step">
      <div class="row inline-row mb-3">
        <div class="col-md-3">
          <label for="nome" class="form-label">Pé dominante:</label>
          <select class="form-control">
            <option value="label">Selecionar</option>
            <option value="direito">Direito</option>
            <option value="esquerdo">Esquerdo</option>
          </select>
        </div>
      </div>
      <div class="row inline-row mb-3">
        <div class="col-md-3">
          <label for="nasc" class="form-label">Posição principal:</label>
          <select class="form-control">
            <option value="label">Selecione a posição</option>
            <option value="gol">Goleiro</option>
            <option value="ld">Lateral Direito</option>
            <option value="le">Lateral Esquerdo</option>
            <option value="ze">Zagueiro Esquerdo</option>
            <option value="zd">Zagueiro Direito</option>
            <option value="vol">Volante</option>
            <option value="mei">Meia</option>
            <option value="pe">Ponta esquerda</option>
            <option value="pd">Ponta direita</option>
            <option value="ata">Atacante</option>
          </select>
        </div>
        <div class="col-md-3">
          <label for="nasc" class="form-label">Posição secundaria:</label>
          <select class="form-control">
            <option value="label">Selecione a posição</option>
            <option value="gol">Goleiro</option>
            <option value="ld">Lateral Direito</option>
            <option value="le">Lateral Esquerdo</option>
            <option value="ze">Zagueiro Esquerdo</option>
            <option value="zd">Zagueiro Direito</option>
            <option value="vol">Volante</option>
            <option value="mei">Meia</option>
            <option value="pe">Ponta esquerda</option>
            <option value="pd">Ponta direita</option>
            <option value="ata">Atacante</option>
          </select>
        </div>
      </div>
      <div class="row inline-row mb-3">
        <div class="col-md-3">
          <label for="altura" class="form-label">Altura:</label>
          <input type="number" id="altura" name="altura" class="form-control" step="any" required="" placeholder="Informe sua altura">
        </div>
        <div class="col-md-3">
          <label for="peso" class="form-label">Peso:</label>
          <input type="number" id="peso" name="peso" class="form-control" step="any" required="" placeholder="Informe seu peso">
        </div>
      </div>
      <div class="row inline-row mb-3">
        <div class="col-md-3">
          <p>Já fez cirurgia?</p>
          <div class="checkbox-wrapper-1">
            <input id="sim" class="substituted" type="checkbox" aria-hidden="true" />
            <label for="sim">Sim</label>
            <input id="nao" class="substituted" type="checkbox" aria-hidden="true" />
            <label for="nao">Não</label>
          </div>
        </div>
      </div>
    </div>

    <!-- Etapa 3 -->
    <div class="form-step">
      <div class="row inline-row mb-3">
        <div class="col-md-8">
          <label for="video" class="form-label">Vídeo de apresentação:</label>
          <input type="link" id="file" name="file" class="form-control" step="any" required="" placeholder="Link do youtube">
        </div>
      </div>
      <div class="row inline-row mb-3">
        <div class="col-md-8">
          <label for="video" class="form-label">Foto 3x4:</label>
          <input type="file" id="file" name="file" class="form-control" step="any" required="" placeholder="Link do youtube">
        </div>
      </div>

    </div>

    <!-- Botões -->
    <div class="buttons">
      <button class="btn btn-secondary" type="button" id="prevBtn" disabled>Voltar</button>
      <button class="btn btn-primary" type="button" id="nextBtn">Avançar</button>
    </div>
  </form>
</div>
<footer>
  <div>
    <p>Grêmio Prudente &copy; 2025. Todos os direitos reservados.</p>
  </div>
</footer>

<script src="{{ asset('js/forms.js') }}"></script>

@endsection