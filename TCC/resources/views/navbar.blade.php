<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grêmio Prudente - Scouting</title>
    <link rel="stylesheet" href="{{ asset('css/style_crud.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-copia.png') }}">
    <!--  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="padrao-global @yield('body-class')">
    <nav class="sidebar">
        <!-- Perfil -->
        <div class="nav-item profile">
            <img src="{{ asset('img/logo-copia.png') }}" alt="Avatar" class="avatar">
            <div class="tooltip">Logo</div>
        </div>

        <!-- Dashboard -->
        <div onclick="window.location.href='{{ route('home.index') }}'" class="nav-item">
            <img src="{{ asset('img/home.png') }}" alt="Jogadores" class="icon">
            <div class="tooltip">Dashboard</div>
        </div>

        <!-- Peneiras -->
        <div onclick="window.location.href='{{ route('peneiras.index') }}'" class="nav-item">
            <img src="{{ asset('img/list.PNG') }}" alt="Jogadores" class="icon">
            <div class="tooltip">Peneiras</div>
        </div>

        <!-- Players -->
        <div onclick="window.location.href='{{ route('jogadores.index') }}'" class="nav-item">
            <img src="{{ asset('img/group.PNG') }}" alt="Jogadores" class="icon">
            <div class="tooltip">Jogadores</div>
        </div>

        <!-- Sair -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <div class="nav-item settings"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <img src="{{ asset('img/vector.PNG') }}" alt="Sair" class="icon">
            <div class="tooltip">Sair</div>
        </div>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        function setActive(clickedItem) {
            // Remove active class from all items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Add active class to clicked item
            clickedItem.classList.add('active');

            // Add a subtle animation effect
            clickedItem.style.transform = 'scale(1.2)';
            setTimeout(() => {
                clickedItem.style.transform = '';
            }, 200);
        }
        function navigateTo(page, clickedItem) {
            // Set active state
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            clickedItem.classList.add('active');

            // Add animation effect
            clickedItem.style.transform = 'scale(1.2)';
            setTimeout(() => {
                clickedItem.style.transform = '';
            }, 200);

            // Navigate to PHP page
            window.location.href = page;
        }
        function loadPage(page, clickedItem) {
            // Set active state
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            clickedItem.classList.add('active');

            // Add animation effect
            clickedItem.style.transform = 'scale(1.2)';
            setTimeout(() => {
                clickedItem.style.transform = '';
            }, 200);

            // Load content via AJAX (optional)
            fetch(page)
                .then(response => response.text())
                .then(data => {
                    document.querySelector('.main-content').innerHTML = data;
                })
                .catch(error => {
                    console.error('Erro ao carregar a página:', error);
                    document.querySelector('.main-content').innerHTML = '<h1>Erro ao carregar a página</h1>';
                });
        }

        // Add some interactive feedback
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function () {
                // Create ripple effect
                const ripple = document.createElement('div');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.6)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.left = '50%';
                ripple.style.top = '50%';
                ripple.style.width = '20px';
                ripple.style.height = '20px';
                ripple.style.marginLeft = '-10px';
                ripple.style.marginTop = '-10px';

                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>