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

  // se for a última etapa, troca para "Finalizar"
  if (currentStep === steps.length - 1) {
    nextBtn.textContent = "Finalizar";
    nextBtn.type = "submit"; // aqui o botão vira submit
  } else {
    nextBtn.textContent = "Avançar";
    nextBtn.type = "button"; // aqui ele volta a ser apenas botão
  }
}

nextBtn.addEventListener("click", () => {
  if (currentStep < steps.length - 1) {
    currentStep++;
    updateForm();
  } else {
    // Última etapa -> transforma em submit
    document.getElementById("signupForm").submit();
  }
});

prevBtn.addEventListener("click", () => {
  if (currentStep > 0) {
    currentStep--;
    updateForm();
  }
});

updateForm();