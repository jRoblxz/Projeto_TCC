<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulário Multi-Etapas</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #ff9a3c, #ff6f61);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background: #fff;
      width: 500px;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .steps {
      display: flex;
      justify-content: space-around;
      margin-bottom: 20px;
    }

    .steps div {
      text-align: center;
      font-size: 14px;
      color: #999;
    }

    .steps .active {
      color: #ff6f61;
      font-weight: bold;
    }

    .form-step {
      display: none;
    }

    .form-step.active {
      display: block;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
    }

    .form-group input, 
    .form-group select, 
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
    }

    .buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .buttons button {
      background: #ff6f61;
      border: none;
      color: #fff;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
    }

    .buttons button:disabled {
      background: #ccc;
      cursor: not-allowed;
    }
  </style>
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
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" placeholder="Enter Full Name">
        </div>
        <div class="form-group">
          <label>Date of Birth</label>
          <input type="date">
        </div>
        <div class="form-group">
          <label>Gender</label>
          <select>
            <option>Male</option>
            <option>Female</option>
          </select>
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
        <button type="button" id="prevBtn" disabled>Voltar</button>
        <button type="button" id="nextBtn">Avançar</button>
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
