<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição Confirmada - Peneira de Futebol</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background elements */
        .bg-animation {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .ball {
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .ball:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
            animation-duration: 15s;
        }

        .ball:nth-child(2) {
            top: 60%;
            right: 20%;
            animation-delay: 2s;
            animation-duration: 18s;
        }

        .ball:nth-child(3) {
            bottom: 20%;
            left: 30%;
            animation-delay: 4s;
            animation-duration: 20s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(100px, -100px) rotate(90deg);
            }
            50% {
                transform: translate(-50px, 100px) rotate(180deg);
            }
            75% {
                transform: translate(-100px, -50px) rotate(270deg);
            }
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease-out 0.3s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .checkmark {
            width: 35px;
            height: 35px;
            stroke-width: 3;
            stroke: white;
            fill: none;
            animation: drawCheck 0.6s ease-out 0.8s both;
        }

        @keyframes drawCheck {
            from {
                stroke-dasharray: 50;
                stroke-dashoffset: 50;
            }
            to {
                stroke-dasharray: 50;
                stroke-dashoffset: 0;
            }
        }

        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 15px;
            animation: fadeIn 0.6s ease-out 0.4s both;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.5;
            animation: fadeIn 0.6s ease-out 0.5s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .info-box {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            animation: fadeIn 0.6s ease-out 0.6s both;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin: 15px 0;
            text-align: left;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .info-text {
            flex: 1;
        }

        .info-text strong {
            display: block;
            color: #333;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .info-text span {
            color: #666;
            font-size: 13px;
        }

        .registration-number {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            animation: fadeIn 0.6s ease-out 0.7s both;
        }

        .registration-number label {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .registration-number .number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 2px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            animation: fadeIn 0.6s ease-out 0.8s both;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .divider {
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            margin: 20px auto;
            border-radius: 2px;
            animation: fadeIn 0.6s ease-out 0.65s both;
        }

        /* Confetti animation */
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #667eea;
            animation: confetti-fall 3s linear infinite;
        }

        .confetti:nth-child(odd) {
            background: #764ba2;
            animation-delay: 0.5s;
        }

        .confetti:nth-child(1) { left: 10%; animation-delay: 0s; }
        .confetti:nth-child(2) { left: 30%; animation-delay: 0.3s; }
        .confetti:nth-child(3) { left: 50%; animation-delay: 0.6s; }
        .confetti:nth-child(4) { left: 70%; animation-delay: 0.9s; }
        .confetti:nth-child(5) { left: 90%; animation-delay: 1.2s; }

        @keyframes confetti-fall {
            0% {
                top: -10%;
                transform: rotate(0deg);
                opacity: 1;
            }
            100% {
                top: 110%;
                transform: rotate(720deg);
                opacity: 0;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
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

    <div class="container">
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
        document.querySelector('.registration-number').addEventListener('click', function() {
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
</body>
</html>