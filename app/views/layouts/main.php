<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        .slide-in { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-home text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900"><?= APP_NAME ?></span>
                    </a>
                </div>
                
                <!-- Navegación -->
                <nav class="hidden md:flex space-x-8">
                    <a href="/" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Inicio
                    </a>
                    <a href="/properties" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Propiedades
                    </a>
                    <?php if (isAuthenticated()): ?>
                        <?php if (hasRole(ROLE_AGENTE)): ?>
                            <a href="/properties/agent/list" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Mis Propiedades
                            </a>
                            <a href="/properties/pending-validation" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Pendientes
                            </a>
                        <?php endif; ?>
                        <a href="/dashboard" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                    <?php endif; ?>
                </nav>
                
                <!-- Menú de usuario -->
                <div class="flex items-center space-x-4">
                    <?php if (isAuthenticated()): ?>
                        <!-- Menú de usuario -->
                        <div class="relative">
                            <button id="user-menu-button" class="w-9 h-9 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold text-lg hover:bg-primary-200 transition-colors">
                                <?= strtoupper(substr($_SESSION['user_nombre'] ?? 'U', 0, 1)) ?>
                            </button>
                            <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50">
                                <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                    <?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario') ?>
                                </div>
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Mi Perfil</a>
                                <?php if (hasRole(ROLE_ADMIN)): ?>
                                    <a href="/admin/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Panel Admin</a>
                                <?php endif; ?>
                                <?php if (hasRole(ROLE_AGENTE)): ?>
                                    <a href="/agente/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Panel Agente</a>
                                <?php endif; ?>
                                <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Cerrar Sesión</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Botones de autenticación -->
                        <a href="/login" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="/register" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Registrarse
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-1">
        <!-- Mensajes flash -->
        <?php $flashMessages = getFlashMessages(); ?>
        <?php if (!empty($flashMessages)): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <?php foreach ($flashMessages as $message): ?>
                    <div class="fade-in mb-4 p-4 rounded-md 
                        <?php if ($message['type'] === 'success'): ?>
                            bg-green-50 border border-green-200 text-green-800
                        <?php elseif ($message['type'] === 'info'): ?>
                            bg-blue-50 border border-blue-200 text-blue-800
                        <?php else: ?>
                            bg-red-50 border border-red-200 text-red-800
                        <?php endif; ?>">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <?php if ($message['type'] === 'success'): ?>
                                    <i class="fas fa-check-circle text-green-400"></i>
                                <?php elseif ($message['type'] === 'info'): ?>
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                <?php else: ?>
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium">
                                    <?= $message['message'] ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Contenido de la página -->
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php else: ?>
            <!-- El contenido se incluye directamente desde la vista -->
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-home text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold"><?= APP_NAME ?></span>
                    </div>
                    <p class="text-gray-300">
                        Plataforma líder en gestión inmobiliaria, conectando clientes y agentes.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Enlaces</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-300 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="/properties" class="text-gray-300 hover:text-white transition-colors">Propiedades</a></li>
                        <li><a href="/about" class="text-gray-300 hover:text-white transition-colors">Acerca de</a></li>
                        <li><a href="/contact" class="text-gray-300 hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                    <div class="space-y-2 text-gray-300">
                        <p><i class="fas fa-envelope mr-2"></i>info@propeasy.com</p>
                        <p><i class="fas fa-phone mr-2"></i>+1 809 555 0123</p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i>Santo Domingo, RD</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Menú de usuario
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');
        
        if (userMenuButton && userMenuDropdown) {
            userMenuButton.addEventListener('click', function() {
                userMenuDropdown.classList.toggle('hidden');
            });
            
            // Cerrar menú al hacer clic fuera
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userMenuDropdown.contains(event.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html> 