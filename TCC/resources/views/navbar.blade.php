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

<body>
    <nav class="sidebar">
        <!-- Perfil -->
        <div class="nav-item profile" onclick="setActive(this)">
            <img src="{{ asset('img/logo-copia.png') }}" alt="Avatar" class="avatar">
            <div class="tooltip">Perfil</div>
        </div>

        <!-- Busca -->
        <div class="nav-item active" onclick="setActive(this)">
            <svg viewBox="0 0 24 24" fill="#ff0000ff">
                <path
                    d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"
                    stroke="#851114" stroke-width="1.5" />
            </svg>
            <div class="tooltip">Buscar</div>
        </div>

        <!-- Dashboard -->
        <div onclick="window.location.href='{{ route('home.index') }}'" class="nav-item">
            <img src="{{ asset('img/RECTANGLE.PNG') }}" alt="Dashboard" class="icon">
            <div class="tooltip">Dashboard</div>
        </div>

        <!-- Players -->
        <div  onclick="window.location.href='{{ route('peneiras.index') }}'" class="nav-item">
            <img src="{{ asset('img/jogadores.PNG') }}" alt="Jogadores" class="icon">
            <div class="tooltip">Jogadores</div>
        </div>

        <!-- Estatísticas -->
        <div class="nav-item" onclick="setActive(this)">
            <svg viewBox="0 0 24 24">
                <path d="M22,21H2V3H4V19H6V17H10V19H12V16H16V19H18V17H22V21Z" stroke="#851114" stroke-width="1.5" />
            </svg>
            <div class="tooltip">Estatísticas</div>
        </div>

        <!-- Mensagens -->
        <div class="nav-item" onclick="setActive(this)">
            <svg viewBox="0 0 24 24">
                <path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4C22,2.89 21.1,2 20,2Z" />
            </svg>
            <div class="tooltip">Mensagens</div>
        </div>

        <!-- Configurações -->
        <div class="nav-item settings" onclick="setActive(this)">
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