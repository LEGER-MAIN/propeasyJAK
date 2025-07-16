<?php
/**
 * Componente: Navbar del Cliente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este componente incluye la navegación principal
 */
?>

<!-- Navbar Principal -->
<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2 group">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center group-hover:bg-primary-700 transition-colors">
                        <i class="fas fa-home text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors"><?= APP_NAME ?></span>
                </a>
            </div>
            
            <!-- Navegación Desktop -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
                
                <a href="/properties" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-building"></i>
                    <span>Propiedades</span>
                </a>
                
                <a href="/agentes" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-users"></i>
                    <span>Agentes</span>
                </a>
                
                <?php if (isAuthenticated()): ?>
                    <a href="/favorites" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-heart"></i>
                        <span>Favoritos</span>
                    </a>
                    
                    <a href="/solicitudes" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-handshake"></i>
                        <span><?= hasRole(ROLE_AGENTE) ? 'Solicitudes' : 'Mis Solicitudes' ?></span>
                    </a>
                    
                    <?php if (hasRole(ROLE_AGENTE)): ?>
                        <a href="/properties/agent/list" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                            <i class="fas fa-list"></i>
                            <span>Mis Propiedades</span>
                        </a>
                        <a href="/properties/pending-validation" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                            <i class="fas fa-clock"></i>
                            <span>Pendientes</span>
                        </a>
                        <a href="/agente/perfil" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                            <i class="fas fa-user-circle"></i>
                            <span>Mi Perfil Público</span>
                        </a>
                    <?php endif; ?>
                    
                    <a href="/dashboard" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <?php if (hasRole(ROLE_CLIENTE)): ?>
                        <a href="/buscar-agentes" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            <span>Buscar Agentes</span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>
            
            <!-- Navegación Mobile y Usuario -->
            <div class="flex items-center space-x-4">
                <?php if (isAuthenticated()): ?>
                    <!-- Chat Rápido -->
                    <button id="chat-toggle" class="relative p-2 text-gray-700 hover:text-primary-600 transition-colors">
                        <i class="fas fa-comments text-lg"></i>
                        <span id="chat-notification" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </button>
                    
                    <!-- Perfil de Usuario -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 transition-colors">
                            <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                                </span>
                            </div>
                            <span class="hidden lg:block text-sm font-medium"><?= $_SESSION['user_name'] ?? 'Usuario' ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <!-- Dropdown de Usuario -->
                        <div class="absolute top-full right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-user"></i>
                                    <span>Mi Perfil</span>
                                </a>
                                <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-cog"></i>
                                    <span>Configuración</span>
                                </a>
                                <hr class="my-1">
                                <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Cerrar Sesión</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Botones de Autenticación -->
                    <a href="/login" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Iniciar Sesión
                    </a>
                    <a href="/register" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        Registrarse
                    </a>
                <?php endif; ?>
                
                <!-- Menú Mobile -->
                <button id="mobile-menu-toggle" class="md:hidden p-2 text-gray-700 hover:text-primary-600 transition-colors">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Menú Mobile -->
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-200">
                <a href="/" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                    <i class="fas fa-home mr-2"></i>Inicio
                </a>
                <a href="/properties" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                    <i class="fas fa-building mr-2"></i>Propiedades
                </a>
                <a href="/agentes" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-2"></i>Agentes
                </a>
                <?php if (isAuthenticated()): ?>
                    <a href="/favorites" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-heart mr-2"></i>Favoritos
                    </a>
                    <a href="/solicitudes" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-handshake mr-2"></i><?= hasRole(ROLE_AGENTE) ? 'Solicitudes' : 'Mis Solicitudes' ?>
                    </a>
                    <a href="/dashboard" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <hr class="my-2">
                    <a href="/profile" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-user mr-2"></i>Mi Perfil
                    </a>
                    <a href="/logout" class="block px-3 py-2 text-red-600 hover:bg-red-50 rounded-md text-base font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                    </a>
                <?php else: ?>
                    <hr class="my-2">
                    <a href="/login" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                    </a>
                    <a href="/register" class="block px-3 py-2 bg-primary-600 text-white hover:bg-primary-700 rounded-md text-base font-medium">
                        <i class="fas fa-user-plus mr-2"></i>Registrarse
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Chat Panel Flotante -->
<?php if (isAuthenticated()): ?>
<div id="chat-panel" class="fixed bottom-4 right-4 w-80 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900">Chat Rápido</h3>
        <button id="chat-close" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="p-4">
        <p class="text-sm text-gray-500 text-center">Chat en desarrollo</p>
    </div>
</div>
<?php endif; ?>

<script>
// Funcionalidad del navbar
document.addEventListener('DOMContentLoaded', function() {
    // Menú mobile
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Chat panel
    const chatToggle = document.getElementById('chat-toggle');
    const chatPanel = document.getElementById('chat-panel');
    const chatClose = document.getElementById('chat-close');
    
    if (chatToggle && chatPanel) {
        chatToggle.addEventListener('click', function() {
            chatPanel.classList.toggle('hidden');
        });
        
        if (chatClose) {
            chatClose.addEventListener('click', function() {
                chatPanel.classList.add('hidden');
            });
        }
    }
    
    // Eliminar favoritos rápidos desde el dropdown
    document.querySelectorAll('.remove-favorite-quick').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const favoritoId = this.dataset.favoritoId;
            if (confirm('¿Eliminar de favoritos?')) {
                removeFavoriteQuick(favoritoId);
            }
        });
    });
});

// Función para eliminar favorito rápidamente
function removeFavoriteQuick(favoritoId) {
    const formData = new FormData();
    formData.append('favorito_id', favoritoId);
    
    fetch('/favorites/eliminar-por-id', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Actualizar contador
            updateFavoriteCounter(data.total_favoritos);
            // Recargar la página para actualizar el dropdown
            setTimeout(() => window.location.reload(), 500);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(() => alert('Error al eliminar el favorito'));
}

// Actualizar contador de favoritos
function updateFavoriteCounter(total) {
    const counters = document.querySelectorAll('.favorite-counter');
    counters.forEach(counter => {
        counter.textContent = total > 99 ? '99+' : total;
        counter.style.display = total > 0 ? 'inline' : 'none';
    });
}
</script> 