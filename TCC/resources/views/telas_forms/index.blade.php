<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grêmio Prudente - Scouting</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-image: url('/img/prudentao.png');
            background-size: cover;   /* cobre toda a tela */
            background-position: center; /* centraliza */
            background-repeat: no-repeat; /* não repete */
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        h1 {
            font-size: 2.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #941B1B;
        }
        
        h2 {
            font-size: 1.3rem;
            font-weight: 400;
            margin-bottom: 30px;
            color: #d9d9d9ff;
        }
        
        .section-title {
            text-align: center;
            margin: 40px 0 30px;
            font-size: 2rem;
            color: #941B1B;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: #941B1B;
            margin: 10px auto;
            border-radius: 2px;
        }
        
        .how-it-works {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .step {
            flex: 1;
            min-width: 300px;
            background: #941B1B;
            border-radius: 0px 75px 0px 75px;
            padding: 25px 0px 0px 20px;
            box-shadow: -5px 5px 20px 5px rgba(0, 0, 0, 0.5);
        }
        .step-2 {
            flex: 1;
            min-width: 300px;
            border-radius: 0px 50px 0px 50px;
            background: #14244D;
            padding: 25px;
            box-shadow: -5px 0 10px -5px rgba(0, 0, 0, 0.5);
        }
        
        .step-number {
            font-size: 3rem;
            font-weight: bold;
            color: #14244D;
            margin-bottom: 15px;
            margin-left: -25px;
            margin-top: -25px;
            width: 100px;
            padding: 0 0px 0px 20px;
            background-color: #941B1B;
        }
        
        .step-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #941B1B;
        }
        
        .step-content {
            color: #efefefff;
        }
        
        .step-content ul {
            list-style-type: none;
            padding-left: 20px;
        }
        
        .step-content li {
            margin-bottom: 8px;
            position: relative;
        }
        
        .step-content li:before {
            content: "•";
            color: #941B1B;
            font-weight: bold;
            display: inline-block;
            width: 1em;
            margin-left: -1em;
        }
        
        .website-section {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #941B1B, #14244D);
            color: white;
            border-radius: 10px;
            margin: 40px 0;
        }
        
        .website-url {
            font-size: 2rem;
            margin: 20px 0;
            color: #fff;
            text-decoration: none;
            display: inline-block;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .website-url:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }
        
        .club-name {
            font-size: 3rem;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            color: #666;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .how-it-works {
                flex-direction: column;
            }
            
            .step {
                min-width: 100%;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            .club-name {
                font-size: 2rem;
            }
            
            .website-url {
                font-size: 1.5rem;
            }
        }
        img.logo {
            height: 30vh;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <img src="{{ asset('img/logo.png') }}" alt="Logo Prudente" class="logo">
            <h1>GRÊMIO PRUDENTE</h1>
            <h2>Seleção de jogadores</h2>
        </div>
    </header>
    
    <div class="container">
        <h2 class="section-title">Como funciona?</h2>
        
        <div class="how-it-works">
            <div class="step">
                <div class="step-2">
                    <div class="step-number">01</div>
                <h3 class="step-title">Inscrição online</h3>
                <div class="step-content">
                    <ul>
                        <li>Preencha o formulário com seus dados pessoais, posição, características e anexo dos documentos solicitados.</li>
                    </ul>
                </div>
                </div>                
            </div>
            
            <div class="step">
                <div class="step-number">02</div>
                <h3 class="step-title">Jogos</h3>
                <div class="step-content">
                    <ul>
                        <li>O jogador passará por uma primeira avaliação por meio de um vídeo de apresentação e depois após habilitação para algum dos times da equipe.</li>
                    </ul>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">03</div>
                <h3 class="step-title">Avaliação</h3>
                <div class="step-content">
                    <ul>
                        <li>Nossa equipe irá avaliar criteriosamente cada etapa do processo e irá selecionar os que chamam atenção.</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="website-section">
            <p>Visite nosso site para mais informações:</p>
            <a href="https://www.gremioscouta.com" class="website-url">www.gremioscouta.com</a>
            <div class="club-name">PRUDENTE</div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>Grêmio Prudente &copy; 2025. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>