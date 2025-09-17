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
      <div id="step-1" class="active">1. Basic Details</div>
      <div id="step-2">2. Contact Details</div>
      <div id="step-3">3. Verification</div>
    </div>

    <!-- Formulário -->
    <form id="signupForm">
      <!-- Etapa 1 -->
      <div class="form-step active">
        <div class="row inline-row mb-3">
            <div class="col-md-8">
                <label for="nome" class="form-label">Nome completo:</label>
                <input type="text" id="nome" name="nome" class="form-control" step="any" required="">
            </div>
            <div class="col-md-2">
                <label for="idade" class="form-label">Idade:</label>
                <input type="number" id="idade" name="idade" class="form-control" step="any" required="">
            </div>            
        </div> 
        <div class="row inline-row mb-3">
            <div class="col-md-3">
                <label for="nasc" class="form-label">Data Nascimento:</label>
                <input type="date" id="nasc" name="nasc" class="form-control" step="any" required="">
            </div>
            <div class="col-md-5">
                <label for="cpf" class="form-label">CPF:</label>
                <input type="number" id="cpf" name="cpf" class="form-control" step="any" required="">
            </div>
            <div class="col-md-4">
                <label for="rg" class="form-label">RG:</label>
                <input type="number" id="rg" name="rg" class="form-control" step="any" required="">
            </div>
        </div> 
        <div class="row inline-row mb-3">
            <div class="col-md-6">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" id="email" name="email" class="form-control" step="any" required="">
            </div>
            <div class="col-md-6">
                <label for="fone" class="form-label">Celular:</label>
                <input type="number" id="fone" name="fone" class="form-control" step="any" required="">
            </div>
        </div> 
      </div>

      <!-- Etapa 2 -->
      <div class="form-step">
        <div class="form-group">
          <label>Email</label>
          <input type="email" placeholder="Enter Email">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" placeholder="Enter Phone">
        </div>
        <div class="form-group">
          <label>Address</label>
          <textarea placeholder="Enter Address"></textarea>
        </div>
      </div>

      <!-- Etapa 3 -->
      <div class="form-step">
        <div class="form-group">
          <label>Upload Document</label>
          <input type="file">
        </div>
        <div class="form-group">
          <label>Verification Code</label>
          <input type="text" placeholder="Enter Code">
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
