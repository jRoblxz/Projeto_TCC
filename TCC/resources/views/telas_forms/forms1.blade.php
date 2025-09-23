@extends('telas_forms.header')
@section('content')
<div class="container-form">
    <!-- Passos -->
    <div class="steps">
      <div id="step-1" class="active">1. Informações básicas</div>
      <div id="step-2">2. Informações jogador</div>
      <div id="step-3">3. Vídeo apresentação</div>
    </div>

    <!--KAYNAN: MENSAGENS DE ERRO AQUI ATRAVES DAS VALIDAÇÕES-->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulário -->
    <form action="{{ route('users.store') }}" method="POST" id="signupForm" enctype="multipart/form-data">
      @csrf
      @method('POST')
      <!-- Etapa 1 -->
      <div class="form-step active">
        <div class="row inline-row mb-3">
          <div class="col-md-8">
            <label for="nome" class="form-label">Nome completo:</label>
            <input type="text" id="nome" name="nome_completo" class="form-control" step="any" required="" placeholder="Informe seu nome" value="{{ old('nome') }}">
          </div>
          <div class="col-md-2">
            <label for="idade" class="form-label">Idade:</label>
            <input type="number" id="idade" name="idade" class="form-control" step="any" required="" placeholder="Idade" value="{{ old('idade') }}">
          </div>
        </div>
        <div class="row inline-row mb-3">
          <div class="col-md-3">
            <label for="nasc" class="form-label">Data Nascimento:</label>
            <input type="date" id="nasc" name="data_nascimento" class="form-control" step="any" required="" value="{{ old('nasc') }}">
          </div>
          <div class="col-md-6">
            <label for="cidade" class="form-label">Cidade:</label>
            <input type="text" id="cidade" name="cidade" class="form-control" step="any" required=""placeholder="Informe sua cidade" value="{{ old('cidade') }}">
          </div>
        </div>
        <div class="row inline-row mb-3">
          <div class="col-md-5">
            <label for="cpf" class="form-label">CPF:</label>
            <input type="number" id="cpf" name="cpf" class="form-control" step="any" required="" placeholder="Informe seu CPF (apenas números)" value="{{ old('cpf') }}">
          </div>
          <div class="col-md-4">
            <label for="rg" class="form-label">RG:</label>
            <input type="number" id="rg" name="rg" class="form-control" step="any" required="" placeholder="Informe seu RG (apenas números" value="{{ old('rg') }}">
          </div>
        </div>
        <div class="row inline-row mb-3">
          <div class="col-md-6">
            <label for="email" class="form-label">E-mail:</label>
            <input type="email" id="email" name="email" class="form-control" step="any" required="" placeholder="Informe seu e-mail" value="{{ old('email') }}">
          </div>
          <div class="col-md-6">
            <label for="fone" class="form-label">Celular:</label>
            <input type="number" id="fone" name="telefone" class="form-control" step="any" required="" placeholder="Informe seu contato" value="{{ old('fone') }}">
          </div>
        </div>
      </div>

      <!-- Etapa 2 -->
      <div class="form-step">
        <div class="row inline-row mb-3">
          <div class="col-md-3">
            <label for="nome" class="form-label">Pé dominante:</label>
            <select class="form-control" id="pe" name="pe_preferido" required>
              <option value="" {{ old('pe') == '' ? 'selected' : '' }}>Selecionar</option>
              <option value="direito" {{ old('pe') == 'direito' ? 'selected' : '' }}>Direito</option>
              <option value="esquerdo" {{ old('pe') == 'esquerdo' ? 'selected' : '' }}>Esquerdo</option>
            </select>
          </div>
        </div>
        <div class="row inline-row mb-3">
          <div class="col-md-3">
            <label for="nasc" class="form-label">Posição principal:</label>
            <select class="form-control" id="posicao_principal" name="posicao_principal" required>
              <option value="" {{ old('posicao_principal') == '' ? 'selected' : '' }}>Selecione a posição</option>
              <option value="Goleiro" {{ old('posicao_principal') == 'Goleiro' ? 'selected' : '' }}>Goleiro</option>
              <option value="Lateral Direito" {{ old('posicao_principal') == 'Lateral Direito' ? 'selected' : '' }}>Lateral Direito</option>
              <option value="Lateral Esquerdo" {{ old('posicao_principal') == 'Lateral Esquerdo' ? 'selected' : '' }}>Lateral Esquerdo</option>
              <option value="Zagueiro" {{ old('posicao_principal') == 'Zagueiro' ? 'selected' : '' }}>Zagueiro Esquerdo</option>
              <option value="Zagueiro" {{ old('posicao_principal') == 'Zagueiro' ? 'selected' : '' }}>Zagueiro Direito</option>
              <option value="Volante" {{ old('posicao_principal') == 'Volante' ? 'selected' : '' }}>Volante</option>
              <option value="Meia Central" {{ old('posicao_principal') == 'Meia Central' ? 'selected' : '' }}>Meia</option>
              <option value="Ponta Esquerda" {{ old('posicao_principal') == 'Ponta Esquerda' ? 'selected' : '' }}>Ponta esquerda</option>
              <option value="Ponta Direita" {{ old('posicao_principal') == 'Ponta Direita' ? 'selected' : '' }}>Ponta direita</option>
              <option value="Centroavante" {{ old('posicao_principal') == 'Centroavante' ? 'selected' : '' }}>Atacante</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="nasc" class="form-label">Posição secundaria:</label>
            <select class="form-control" id="posicao_secundaria" name="posicao_secundaria" required>
              <option value="" {{ old('posicao_secundaria') == '' ? 'selected' : '' }}>Selecione a posição</option>
              <option value="Goleiro" {{ old('posicao_principal') == 'Goleiro' ? 'selected' : '' }}>Goleiro</option>
              <option value="Lateral Direito" {{ old('posicao_principal') == 'Lateral Direito' ? 'selected' : '' }}>Lateral Direito</option>
              <option value="Lateral Esquerdo" {{ old('posicao_principal') == 'Lateral Esquerdo' ? 'selected' : '' }}>Lateral Esquerdo</option>
              <option value="Zagueiro" {{ old('posicao_principal') == 'Zagueiro' ? 'selected' : '' }}>Zagueiro Esquerdo</option>
              <option value="Zagueiro" {{ old('posicao_principal') == 'Zagueiro' ? 'selected' : '' }}>Zagueiro Direito</option>
              <option value="Volante" {{ old('posicao_principal') == 'Volante' ? 'selected' : '' }}>Volante</option>
              <option value="Meia Central" {{ old('posicao_principal') == 'Meia Central' ? 'selected' : '' }}>Meia</option>
              <option value="Ponta Esquerda" {{ old('posicao_principal') == 'Ponta Esquerda' ? 'selected' : '' }}>Ponta esquerda</option>
              <option value="Ponta Direita" {{ old('posicao_principal') == 'Ponta Direita' ? 'selected' : '' }}>Ponta direita</option>
              <option value="Centroavante" {{ old('posicao_principal') == 'Centroavante' ? 'selected' : '' }}>Atacante</option>
            </select>
          </div>
        </div>
        <div class="row inline-row mb-3">
          <div class="col-md-3">
            <label for="altura" class="form-label">Altura:</label>
            <input type="number" id="altura" name="altura_cm" class="form-control" step="any" required="" placeholder="Informe sua altura" value="{{ old('altura') }}">
          </div>
          <div class="col-md-3">
            <label for="peso" class="form-label">Peso:</label>
            <input type="number" id="peso" name="peso_kg" class="form-control" step="any" required="" placeholder="Informe seu peso" value="{{ old('peso') }}">
          </div>
        </div>
        <div class="row inline-row mb-3">
          <div class="col-md-3">
            <p>Já fez cirurgia?</p>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="historico_lesoes_cirurgias" id="cirurgia_sim" value="sim" required {{ old('cirurgia') == 'sim' ? 'checked' : '' }}>
              <label class="form-check-label" for="cirurgia_sim">Sim</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="historico_lesoes_cirurgias" id="cirurgia_nao" value="nao" required {{ old('cirurgia') == 'nao' ? 'checked' : '' }}>
              <label class="form-check-label" for="cirurgia_nao">Não</label>
            </div>
          </div>
        </div>
      </div>

      <!-- Etapa 3 -->
      <div class="form-step">
        <div class="row inline-row mb-3">
          <div class="col-md-8">
            <label for="video" class="form-label">Vídeo de apresentação:</label>
            <input type="url" id="link" name="video_apresentacao_url" class="form-control" required="" placeholder="Link do youtube" value="{{ old('link') }}"> <!--KAYNAN: TIPO DE DADO AQUI-->
          </div>
        </div>
        <div class="row inline-row mb-3">
          <div class="col-md-8">
            <label for="video" class="form-label">Foto 3x4:</label>
            <input type="file" id="img" name="foto_perfil_url" class="form-control" step="any" required="" placeholder="Imagem 3x4"> <!--KAYNAN: TIPO DE DADO AQUI-->
          </div>
        </div>
       
      </div>

      <!-- Botões -->
      <div class="buttons">
        <button class="btn btn-secondary" type="button" id="prevBtn" disabled>Voltar</button>
        <button class="btn btn-primary" type="button" id="nextBtn">Avançar</button>

        <button class="btn btn-primary" type="submit" id="finish">mandar</button>

        @if (session('success'))
          <p style="color: #086;">
            {{ session('success') }}
          </p>
        @endif
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