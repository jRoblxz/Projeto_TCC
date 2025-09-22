<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grêmio Prudente - Scouting</title>
    <link rel="stylesheet" href="{{ asset('css/style_crud.css') }}">
    <link rel="icon" type="imag/png" href="{{ asset('img/logo-copia.png') }}">
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
            <svg viewBox="0 0 24 24">
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
            <div class="tooltip">Buscar</div>
        </div>

        <!-- Dashboard -->
        <div class="nav-item" onclick="setActive(this)">
            <svg viewBox="0 0 24 24">
                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
            </svg>
            <div class="tooltip">Dashboard</div>
        </div>

        <!-- Documentos -->
        <div class="nav-item" onclick="setActive(this)">
            <svg viewBox="0 0 24 24">
                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
            </svg>
            <div class="tooltip">Documentos</div>
        </div>

        <!-- Estatísticas -->
        <div class="nav-item" onclick="setActive(this)">
            <svg viewBox="0 0 24 24">
                <path d="M22,21H2V3H4V19H6V17H10V19H12V16H16V19H18V17H22V21Z"/>
            </svg>
            <div class="tooltip">Estatísticas</div>
        </div>

        <!-- Mensagens -->
        <div class="nav-item" onclick="setActive(this)">
            <svg viewBox="0 0 24 24">
                <path d="M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4C22,2.89 21.1,2 20,2Z"/>
            </svg>
            <div class="tooltip">Mensagens</div>
        </div>

        <!-- Configurações -->
        <div class="nav-item settings" onclick="setActive(this)">
            <svg viewBox="0 0 24 24">
                <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
            </svg>
            <div class="tooltip">Configurações</div>
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
            item.addEventListener('click', function() {
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