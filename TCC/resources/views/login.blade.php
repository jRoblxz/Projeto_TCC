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
                <img src="{{ asset('img/logo.png') }}" alt="Logo Prudente" class="logo">
            </div>
            <div class="right">
                <h3>BEM VINDO DE VOLTA!</h3>
                <hr>
                <p>Faça seu login</p>
                <form>
                    <div class="input-group">
                        <input type="text" placeholder="Usuário">
                    </div>
                    <div class="input-group">
                        <input type="password" placeholder="Senha">
                    </div>
                    <button type="submit" class="btn">ENTRAR</button>
                </form>
                <a href="#" class="forgot">Esqueceu a senha</a>
            </div>
        </div>
    </div>
</body>

</html>