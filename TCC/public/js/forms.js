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