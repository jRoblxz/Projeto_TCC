<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
  <title>Formulário Multi-Etapas</title>
</head>

<body>
  <div class="container">
    <!-- Passos -->
    <div class="steps">
      <div id="step-1" class="active">1. Informações básicas</div>
      <div id="step-2">2. Informações jogador</div>
      <div id="step-3">3. Vídeo apresentação</div>
    </div>

    <!-- Formulário -->
    <form action="{{ route('user-store') }}" method="POST" id="signupForm"> #KAYNAN: ROTA AQUI
      @csrf #KAYNAN: SEGURANÇA
      @method('POST') #KAYNAN: TIPO DE REQUISIÇÃO
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
            <input type="text" id="cidade" name="cidade" class="form-control" step="any" required=""placeholder="Informe sua cidade">
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
            <select class="form-control" id="pe" name="pe" required> #KAYNAN: NOME DO CAMPO AQUI PARA VALIDAR
              <option value="label">Selecionar</option>
              <option value="direito">Direito</option>
              <option value="esquerdo">Esquerdo</option>
            </select>
          </div>
        </div>
        <div class="row inline-row mb-3">
          <div class="col-md-3">
            <label for="nasc" class="form-label">Posição principal:</label>
            <select class="form-control" id="posicao_principal" name="posicao_principal" required> #KAYNAN: NOME DO CAMPO AQUI PARA VALIDAR
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
            <select class="form-control" id="posicao_secundaria" name="posicao_secundaria" required> #KAYNAN: NOME DO CAMPO AQUI PARA VALIDAR
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
            <div class="form-check">
              <input class="form-check-input" type="radio" name="cirurgia" id="cirurgia_sim" value="sim" required>
              <label class="form-check-label" for="cirurgia_sim">Sim</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="cirurgia" id="cirurgia_nao" value="nao" required>
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
            <input type="link" id="file" name="link" class="form-control" step="any" required="" placeholder="Link do youtube"> #KAYNAN: TIPO DE DADO AQUI
          </div>
        </div>
        <div class="row inline-row mb-3">
          <div class="col-md-8">
            <label for="video" class="form-label">Foto 3x4:</label>
            <input type="file" id="file" name="img" class="form-control" step="any" required="" placeholder="Link do youtube"> #KAYNAN: TIPO DE DADO AQUI
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

  <script>
    const steps = document.querySelectorAll(".form-step");
    const stepIndicators = document.querySelectorAll(".steps div");
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");
    let currentStep = 0;

    function updateForm() {
      steps.forEach((step, index) => {
        step.classList.toggle("active", index === currentStep);
        stepIndicators[index].classList.toggle("active", index === currentStep);
      });

      prevBtn.disabled = currentStep === 0;
      nextBtn.textContent = currentStep === steps.length - 1 ? "Finalizar" : "Avançar";
    }

    nextBtn.addEventListener("click", () => {
      if (currentStep < steps.length - 1) {
        currentStep++;
        updateForm();
      } else {
        alert("Formulário concluído!");
        document.getElementById("signupForm").reset();
        currentStep = 0;
        updateForm();
      }
    });

    prevBtn.addEventListener("click", () => {
      if (currentStep > 0) {
        currentStep--;
        updateForm();
      }
    });

    updateForm();
  </script>
</body>

</html>