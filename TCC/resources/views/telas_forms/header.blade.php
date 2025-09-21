
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grêmio Prudente - Scouting</title>
    <link rel="stylesheet" href="{{ asset('css/style_index.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="imag/png" href="{{ asset('img/logo-copia.png') }}">
    
</head>

<body>
    <header>
        <div class="container-header">
            <img src="{{ asset('img/logo-copia.png') }}" alt="Logo Prudente" class="logo">
            <h1 class="club-name">GRÊMIO PRUDENTE</h1>
            <h2 class="h2-1">Seleção de jogadores</h2>
        </div>
    </header>
    @yield('content')
</body>

</html>
