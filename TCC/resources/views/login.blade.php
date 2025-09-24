<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style_login.css') }}">


</head>

<body>
    <div class="container">
        <div class="login-box">
            <div class="left">
                <img src="{{asset('img/logo-copia.png')}}" alt="Logo Prudente" class="logo">
                <h2>PRUDENTE</h2>
            </div>
            <div class="right">
                <h3>BEM VINDO DE VOLTA!</h3>
                <p>Faça seu login</p>
                <form>
                    <div class="input-group">
                        <input type="text" placeholder="Usuário">
                    </div>
                    <div class="input-group">
                        <input type="password" placeholder="Senha">
                    </div>
                    <button class="button" onclick="window.location.href='home'">
                        <div class="bgContainer">
                            <span>Hover</span>
                            <span>Hover</span>
                        </div>
                        <div class="arrowContainer">
                            <svg width="25" height="25" viewBox="0 0 45 38" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M43.7678 20.7678C44.7441 19.7915 44.7441 18.2085 43.7678 17.2322L27.8579 1.32233C26.8816 0.34602 25.2986 0.34602 24.3223 1.32233C23.346 2.29864 23.346 3.88155 24.3223 4.85786L38.4645 19L24.3223 33.1421C23.346 34.1184 23.346 35.7014 24.3223 36.6777C25.2986 37.654 26.8816 37.654 27.8579 36.6777L43.7678 20.7678ZM0 21.5L42 21.5V16.5L0 16.5L0 21.5Z"
                                    fill="black"></path>
                            </svg>
                        </div>
                    </button>
                </form>
                <a href="#" class="forgot">Esqueceu a senha</a>
            </div>
        </div>
    </div>
</body>

</html>