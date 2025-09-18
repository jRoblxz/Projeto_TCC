<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peneira - Grêmio</title>
    <link rel="stylesheet" href="{{ asset('css/como-funciona.css') }}">
</head>
<body>
    <div class="container">
        <div class="overlay"></div>
        <header>
            <img src="{{ asset('images/logo.png') }}" alt="Grêmio Prudente" class="logo">
            <h1>GRÊMIO PRUDENTE</h1>
            <h2>Avaliação de jogadores</h2>
        </header>

        <main>
            <h3 class="subtitle">Como funciona?</h3>
            <div class="steps">
                <div class="card">
                    <span class="number">01</span>
                    <h4>Inscrição online</h4>
                    <p>
                        Preencha o formulário com seus dados pessoais, posição, histórico e anexo dos documentos solicitados.
                    </p>
                </div>
                <div class="card">
                    <span class="number">02</span>
                    <h4>Jogos</h4>
                    <p>
                        O jogador passará por uma primeira avaliação por meio de apresentações/vídeos e depois será direcionado para algum dos times da peneira.
                    </p>
                </div>
                <div class="card">
                    <span class="number">03</span>
                    <h4>Avaliação</h4>
                    <p>
                        Nossa equipe irá avaliar detalhadamente cada etapa da peneira e irá selecionar os que chamarem atenção.
                    </p>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/como-funciona.js') }}"></script>
</body>
</html>
