@extends('telas_forms.header')
@section('content')
<div class="container">
    <h2 class="section-title">Como funciona?</h2>

    <div class="how-it-works">
        <div class="step">
            <div class="step-2">
                <div class="header-step">
                    <div class="step-number">01</div>
                    <img src="{{ asset('img/google-forms.png') }}" alt="Icon form" class="icon">
                </div>
                <h3 class="step-title">Inscrição online</h3>
                <div class="step-content">
                    <p class="inst">Preencha o formulário com seus dados pessoais, posição, características e anexo dos documentos solicitados. <br><strong>.</strong></p>
                </div>
            </div>
        </div>

        <div class="step">
            <div class="step-2">
                <div class="header-step">
                    <div class="step-number">02</div>
                    <img src="{{ asset('img/football.png') }}" alt="Icon fut" class="icon-2">
                </div>
                <h3 class="step-title">Jogos</h3>
                <div class="step-content">
                    <p class="inst">O jogador passará por uma primeira avaliação por meio de um vídeo de apresentação e depois após habilitação para algum dos times da equipe.</p>
                </div>
            </div>
        </div>

        <div class="step">
            <div class="step-2">
                <div class="header-step">
                    <div class="step-number">03</div>
                    <img src="{{ asset('img/defensive-wall.png') }}" alt="Icon team" class="icon-2">
                </div>
                <h3 class="step-title">Avaliação</h3>
                <div class="step-content">
                    <p class="inst">Nossa equipe irá avaliar criteriosamente cada etapa do processo e irá selecionar os que chamam atenção.<br><strong>.</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="website-section">
        <p>Visite nosso site para mais informações:</p>
        <a href="/form1" class="website-url">INSCREVA-SE</a>
        <div class="club-name">PRUDENTE</div>
    </div>
</div>

<footer>
    <div class="container">
        <p>Grêmio Prudente &copy; 2025. Todos os direitos reservados.</p>
    </div>
</footer>
@endsection