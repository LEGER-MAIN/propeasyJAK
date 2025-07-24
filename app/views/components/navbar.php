<?php
/**
 * Componente: Navbar del Cliente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este componente incluye la navegación principal
 */
?>

<!-- Navbar Principal -->
<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40" style="background-color: var(--bg-light) !important; border-bottom-color: var(--color-gris-claro) !important; box-shadow: var(--shadow-sm) !important;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2 group">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center group-hover:bg-primary-700 transition-colors" style="background-color: var(--color-azul-marino);">
                        <i class="fas fa-home text-white text-sm"></i>
                    </div>
                    <span class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-primary-600 transition-colors" style="color: var(--color-azul-marino);"><?= APP_NAME ?></span>
                </a>
            </div>
            
            <!-- Navegación Desktop -->
            <nav class="hidden lg:flex items-center space-x-6">
                <a href="/" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2" style="color: var(--text-primary);">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
                
                <a href="/properties" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2" style="color: var(--text-primary);">
                    <i class="fas fa-building"></i>
                    <span>Propiedades</span>
                </a>
                
                <a href="/agentes" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2" style="color: var(--text-primary);">
                    <i class="fas fa-users"></i>
                    <span>Agentes</span>
                </a>
                
                <?php if (isAuthenticated()): ?>
                    <?php if (hasRole(ROLE_CLIENTE)): ?>
                    <a href="/cliente/mis-ventas" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2" style="color: var(--text-primary);">
                        <i class="fas fa-paper-plane"></i>
                        <span>Mis Ventas</span>
                    </a>
                    <?php endif; ?>
                    
                    <a href="/solicitudes" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2" style="color: var(--text-primary);">
                        <i class="fas fa-handshake"></i>
                        <span><?= hasRole(ROLE_AGENTE) ? 'Solicitudes' : 'Mis Solicitudes' ?></span>
                    </a>
                    
                    <?php if (hasRole(ROLE_AGENTE)): ?>
                    <a href="/properties/agent/list" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2" style="color: var(--text-primary);">
                        <i class="fas fa-list"></i>
                        <span>Mis Propiedades</span>
                    </a>
                    <a href="/appointments" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2" style="color: var(--text-primary);">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Citas</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_CLIENTE)): ?>
                    <a href="/appointments" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2" style="color: var(--text-primary);">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Mis Citas</span>
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= hasRole(ROLE_AGENTE) ? '/agente/dashboard' : (hasRole(ROLE_CLIENTE) ? '/cliente/dashboard' : '/dashboard') ?>" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center gap-2" style="color: var(--text-primary);">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                <?php endif; ?>
            </nav>
            
            <!-- Navegación Mobile y Usuario -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                <?php if (isAuthenticated()): ?>
                    <!-- Chat Rápido - Solo visible en desktop -->
                    <a href="/chat" class="hidden md:block relative p-2 text-gray-700 hover:text-primary-600 transition-colors" style="color: var(--text-primary);" title="Ir al Chat">
                        <i class="fas fa-comments text-lg"></i>
                        <span id="chat-notification" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                        <span id="ws-status" class="absolute -bottom-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white"></span>
                    </a>
                    
                    <!-- Perfil de Usuario Desktop -->
                    <div class="hidden lg:block relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 transition-colors" style="color: var(--text-primary);">
                            <?php if (!empty($_SESSION['user_foto_perfil'])): ?>
                                <img class="w-8 h-8 rounded-full object-cover border-2 border-gray-200" 
                                     src="<?= htmlspecialchars($_SESSION['user_foto_perfil']) ?>" 
                                     alt="Foto de perfil">
                            <?php else: ?>
                            <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center" style="background-color: var(--color-azul-marino);">
                                <span class="text-white text-sm font-medium">
                                    <?= strtoupper(substr($_SESSION['user_nombre'] ?? 'U', 0, 1)) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            <span class="text-sm font-medium"><?= ($_SESSION['user_nombre'] ?? '') . ' ' . ($_SESSION['user_apellido'] ?? '') ?: 'Usuario' ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <!-- Dropdown de Usuario -->
                        <div class="absolute top-full right-0 mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-user"></i>
                                    <span>Mi Perfil</span>
                                </a>

                                <?php if (hasRole(ROLE_CLIENTE)): ?>
                                <a href="/favorites" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-heart"></i>
                                    <span>Mis Favoritos</span>
                                </a>
                                <a href="/cliente/mis-ventas" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-home"></i>
                                    <span>Mis Ventas</span>
                                </a>
                                <?php endif; ?>

                                <a href="/reportes/mis-reportes" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-clipboard-list"></i>
                                    <span>Mis Reportes</span>
                                </a>
                                <a href="/reportes/crear" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Reportar Problema</span>
                                </a>
                                
                                <a href="/chat" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-comments"></i>
                                    <span>Chat</span>
                                </a>
                                

                                

                                
                                <hr class="my-1">
                                <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Cerrar Sesión</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Perfil de Usuario Mobile/Tablet -->
                    <div class="lg:hidden flex items-center space-x-2">
                        <?php if (!empty($_SESSION['user_foto_perfil'])): ?>
                            <img class="w-8 h-8 rounded-full object-cover border-2 border-gray-200" 
                                 src="<?= htmlspecialchars($_SESSION['user_foto_perfil']) ?>" 
                                 alt="Foto de perfil">
                        <?php else: ?>
                        <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">
                                <?= strtoupper(substr($_SESSION['user_nombre'] ?? 'U', 0, 1)) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        <span class="hidden sm:block text-sm font-medium text-gray-700"><?= ($_SESSION['user_nombre'] ?? '') . ' ' . ($_SESSION['user_apellido'] ?? '') ?: 'Usuario' ?></span>
                    </div>
                <?php else: ?>
                    <!-- Botones de Autenticación Desktop -->
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="/login" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors" style="color: var(--text-primary);">
                            Iniciar Sesión
                        </a>
                        <a href="/register" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors" style="background-color: var(--color-azul-marino);">
                            Registrarse
                        </a>
                    </div>
                    
                    <!-- Botones de Autenticación Mobile -->
                    <div class="md:hidden flex items-center space-x-2">
                        <a href="/login" class="text-gray-700 hover:text-primary-600 p-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-sign-in-alt"></i>
                        </a>
                        <a href="/register" class="bg-primary-600 hover:bg-primary-700 text-white p-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-user-plus"></i>
                        </a>
                    </div>
                <?php endif; ?>
                
                <!-- Menú Mobile -->
                <button id="mobile-menu-toggle" class="lg:hidden p-2 text-gray-700 hover:text-primary-600 transition-colors">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Menú Mobile -->
        <div id="mobile-menu" class="lg:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-200">
                <!-- Enlaces principales -->
                <a href="/" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                    <i class="fas fa-home mr-3"></i>Inicio
                </a>
                <a href="/properties" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                    <i class="fas fa-building mr-3"></i>Propiedades
                </a>
                <a href="/agentes" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                    <i class="fas fa-users mr-3"></i>Agentes
                </a>
                
                <?php if (isAuthenticated()): ?>
                    <hr class="my-2 border-gray-200">
                    
                    <!-- Enlaces de usuario autenticado -->
                    <a href="/solicitudes" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-handshake mr-3"></i><?= hasRole(ROLE_AGENTE) ? 'Solicitudes' : 'Mis Solicitudes' ?>
                    </a>
                    
                    <?php if (hasRole(ROLE_CLIENTE)): ?>
                    <a href="/cliente/mis-ventas" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-paper-plane mr-3"></i>Mis Ventas
                    </a>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_AGENTE)): ?>
                    <a href="/properties/agent/list" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-list mr-3"></i>Mis Propiedades
                    </a>
                    <a href="/appointments" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-calendar-alt mr-3"></i>Citas
                    </a>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_CLIENTE)): ?>
                    <a href="/appointments" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-calendar-alt mr-3"></i>Mis Citas
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= hasRole(ROLE_AGENTE) ? '/agente/dashboard' : (hasRole(ROLE_CLIENTE) ? '/cliente/dashboard' : '/dashboard') ?>" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                    </a>
                    
                    <a href="/chat" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-comments mr-3"></i>Chat
                    </a>
                    
                    <?php if (hasRole(ROLE_AGENTE)): ?>
                        <hr class="my-2 border-gray-200">
                        <div class="px-3 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Panel de Agente</div>
                        <a href="/properties/agent/list" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                            <i class="fas fa-list mr-3"></i>Mis Propiedades
                        </a>
                        <a href="/properties/pending-validation" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                            <i class="fas fa-clock mr-3"></i>Pendientes
                        </a>
                        <a href="/appointments" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                            <i class="fas fa-calendar-alt mr-3"></i>Mis Citas
                        </a>
                        <a href="/agente/perfil" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                            <i class="fas fa-user-circle mr-3"></i>Mi Perfil Público
                        </a>
                    <?php endif; ?>
                    
                    <?php if (hasRole(ROLE_CLIENTE)): ?>
                        <hr class="my-2 border-gray-200">
                        <a href="/buscar-agentes" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                            <i class="fas fa-search mr-3"></i>Buscar Agentes
                        </a>
                        <a href="/appointments" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                            <i class="fas fa-calendar-alt mr-3"></i>Mis Citas
                        </a>
                    <?php endif; ?>
                    
                    <hr class="my-2 border-gray-200">
                    <div class="px-3 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Cuenta</div>
                    <a href="/profile" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-user mr-3"></i>Mi Perfil
                    </a>

                    <a href="/favorites" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-heart mr-3"></i>Mis Favoritos
                    </a>

                    <a href="/reportes/mis-reportes" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-clipboard-list mr-3"></i>Mis Reportes
                    </a>
                    <a href="/reportes/crear" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-exclamation-triangle mr-3"></i>Reportar Problema
                    </a>
                    <a href="/chat" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-comments mr-3"></i>Chat
                    </a>
                    <a href="/logout" class="block px-3 py-2 text-red-600 hover:bg-red-50 rounded-md text-base font-medium">
                        <i class="fas fa-sign-out-alt mr-3"></i>Cerrar Sesión
                    </a>
                <?php else: ?>
                    <hr class="my-2 border-gray-200">
                    <div class="px-3 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acceso</div>
                    <a href="/login" class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md text-base font-medium">
                        <i class="fas fa-sign-in-alt mr-3"></i>Iniciar Sesión
                    </a>
                    <a href="/register" class="block px-3 py-2 bg-primary-600 text-white hover:bg-primary-700 rounded-md text-base font-medium">
                        <i class="fas fa-user-plus mr-3"></i>Registrarse
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>



<script>
// Funcionalidad del navbar
document.addEventListener('DOMContentLoaded', function() {
    // Menú mobile
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            // Cambiar icono del botón
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
        
        // Cerrar menú al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!mobileMenuToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                const icon = mobileMenuToggle.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            }
        });
        
        // Cerrar menú al cambiar tamaño de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) { // lg breakpoint
                mobileMenu.classList.add('hidden');
                const icon = mobileMenuToggle.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            }
        });
    }
    
    // Chat notification (mantener funcionalidad de notificaciones)
    const chatNotification = document.getElementById('chat-notification');
    
    // Función para actualizar notificaciones de chat
    function updateChatNotifications() {
        // Aquí puedes agregar lógica para obtener notificaciones no leídas
        // Por ahora lo dejamos como placeholder
    }
    
    // Actualizar notificaciones cada 30 segundos
    setInterval(updateChatNotifications, 30000);
    
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
