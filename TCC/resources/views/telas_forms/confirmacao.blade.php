@extends('telas_forms.header')
@section('content')

    <div class="bg-animation">
        <div class="ball"></div>
        <div class="ball"></div>
        <div class="ball"></div>
    </div>

    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>

    <div class="container-confirm">
        <div class="success-icon">
            <svg class="checkmark" viewBox="0 0 52 52">
                <path d="M14 27 L 22 35 L 38 16"></path>
            </svg>
        </div>

        <h1>Inscrição Confirmada!</h1>
        <p class="subtitle">
            Parabéns! Sua inscrição na peneira foi realizada com sucesso.
            Prepare-se para mostrar seu talento!
        </p>

        <div class="registration-number">
            <label>Número de Inscrição</label>
            <div class="number">PEN-2024-0847</div>
        </div>

        <div class="divider"></div>

        <div class="info-box">
            <div class="info-item">
                <div class="info-icon">
                    📧
                </div>
                <div class="info-text">
                    <strong>Confirmação por E-mail</strong>
                    <span>Enviamos todos os detalhes para seu e-mail cadastrado</span>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    📅
                </div>
                <div class="info-text">
                    <strong>Data da Peneira</strong>
                    <span>15 de Dezembro de 2024 - 08:00h</span>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    📍
                </div>
                <div class="info-text">
                    <strong>Local</strong>
                    <span>Centro de Treinamento - Campo Principal</span>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    👕
                </div>
                <div class="info-text">
                    <strong>O que levar</strong>
                    <span>Chuteira, caneleira, roupa esportiva e documento com foto</span>
                </div>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-secondary" onclick="window.print()">
                📄 Imprimir
            </button>
            <a href="#" class="btn btn-primary">
                ✅ Concluir
            </a>
        </div>
    </div>

    <script>
        // Add some interactivity
        setTimeout(() => {
            const confettis = document.querySelectorAll('.confetti');
            confettis.forEach(c => c.style.display = 'none');
        }, 3000);

        // Copy registration number on click
        document.querySelector('.registration-number').addEventListener('click', function () {
            const number = this.querySelector('.number').textContent;
            navigator.clipboard.writeText(number).then(() => {
                const originalText = this.querySelector('.number').textContent;
                this.querySelector('.number').textContent = '✓ Copiado!';
                setTimeout(() => {
                    this.querySelector('.number').textContent = originalText;
                }, 2000);
            });
        });
    </script>
@endsection