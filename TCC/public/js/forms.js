const steps = document.querySelectorAll(".form-step");
    const stepIndicators = document.querySelectorAll(".steps div");
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");
    const signupForm = document.getElementById("signupForm");
    let currentStep = 0;

    // Função para atualizar a exibição dos passos e botões
    function updateForm() {
        steps.forEach((step, index) => {
            step.classList.toggle("active", index === currentStep);
            stepIndicators[index].classList.toggle("active", index === currentStep);
        });

        prevBtn.disabled = currentStep === 0;

        // Altera o texto do botão se for a última etapa
        if (currentStep === steps.length - 1) {
            nextBtn.textContent = "Finalizar";
        } else {
            nextBtn.textContent = "Avançar";
        }
    }

    // Evento de clique no botão "Avançar/Finalizar"
    nextBtn.addEventListener("click", () => {
        // Se NÃO for a última etapa, apenas avance
        if (currentStep < steps.length - 1) {
            currentStep++;
            updateForm();
        } else {
            // Se for a última etapa, envie o formulário
            signupForm.submit();
        }
    });

    // Evento de clique no botão "Voltar"
    prevBtn.addEventListener("click", () => {
        if (currentStep > 0) {
            currentStep--;
            updateForm();
        }
    });

    // Inicializa o formulário no estado correto
    updateForm();