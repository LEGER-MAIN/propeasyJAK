/**
 * Sistema de Favoritos - PropEasy
 * Maneja la funcionalidad de favoritos para propiedades
 */

class FavoriteSystem {
    constructor() {
        console.log('🔧 Inicializando sistema de favoritos...');
        this.init();
    }
    
    init() {
        console.log('🔧 Inicializando componentes...');
        this.loadFavoriteStates();
        this.updateFavoriteCount();
        this.bindEvents();
        console.log('✅ Sistema de favoritos inicializado');
    }
    
    /**
     * Vincular eventos a los botones de favorito
     */
    bindEvents() {
        console.log('🔧 Vinculando eventos de favoritos...');
        document.addEventListener('click', (e) => {
            if (e.target.closest('.favorite-btn')) {
                console.log('🖱️ Click en botón de favorito detectado');
                e.preventDefault();
                const button = e.target.closest('.favorite-btn');
                const propertyId = button.dataset.propertyId;
                console.log(`🏠 ID de propiedad: ${propertyId}`);
                if (propertyId) {
                    this.toggleFavorite(propertyId, button);
                } else {
                    console.error('❌ No se encontró ID de propiedad en el botón');
                }
            }
        });
        console.log('✅ Eventos de favoritos vinculados');
    }
    
    /**
     * Cargar el estado de favoritos para todas las propiedades en la página
     */
    loadFavoriteStates() {
        console.log('🔍 Cargando estados de favoritos...');
        const favoriteButtons = document.querySelectorAll('.favorite-btn');
        console.log(`🔍 Encontrados ${favoriteButtons.length} botones de favorito`);
        
        favoriteButtons.forEach(btn => {
            const propertyId = btn.dataset.propertyId;
            console.log(`🔍 Verificando botón con ID: ${propertyId}`);
            if (propertyId) {
                this.checkFavoriteState(propertyId, btn);
            } else {
                console.error('❌ Botón sin ID de propiedad:', btn);
            }
        });
    }
    
    /**
     * Verificar si una propiedad está en favoritos
     */
    checkFavoriteState(propertyId, button) {
        fetch(`/favorites/verify/${propertyId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.is_favorite) {
                button.classList.add('active');
                button.querySelector('i').classList.remove('text-gray-400');
                button.querySelector('i').classList.add('text-red-500');
                button.dataset.isFavorite = 'true';
            } else {
                button.classList.remove('active');
                button.querySelector('i').classList.remove('text-red-500');
                button.querySelector('i').classList.add('text-gray-400');
                button.dataset.isFavorite = 'false';
            }
        })
        .catch(error => {
            console.error('Error checking favorite state:', error);
        });
    }
    
    /**
     * Alternar estado de favorito
     */
    toggleFavorite(propertyId, button) {
        console.log(`🔄 Alternando favorito para propiedad ${propertyId}`);
        const isFavorite = button.dataset.isFavorite === 'true';
        const url = isFavorite ? '/favorites/remove' : '/favorites/add';
        console.log(`📡 Enviando ${isFavorite ? 'REMOVE' : 'ADD'} a ${url}`);
        
        // Mostrar loading
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin text-gray-400"></i>';
        button.disabled = true;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                property_id: propertyId
            })
        })
        .then(response => {
            console.log(`📥 Response status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log(`📦 Response data:`, data);
            if (data.success) {
                if (isFavorite) {
                    // Remover de favoritos
                    button.classList.remove('active');
                    button.querySelector('i').classList.remove('text-red-500');
                    button.querySelector('i').classList.add('text-gray-400');
                    button.dataset.isFavorite = 'false';
                    
                    // Si estamos en la página de favoritos, remover la tarjeta
                    if (window.location.pathname === '/favorites') {
                        const card = button.closest('.property-card, .bg-white');
                        if (card) {
                            card.style.opacity = '0.5';
                            setTimeout(() => {
                                card.remove();
                                this.checkEmptyState();
                            }, 300);
                        }
                    }
                } else {
                    // Agregar a favoritos
                    button.classList.add('active');
                    button.querySelector('i').classList.remove('text-gray-400');
                    button.querySelector('i').classList.add('text-red-500');
                    button.dataset.isFavorite = 'true';
                }
                
                // Mostrar notificación
                this.showNotification(data.message, 'success');
                
                // Actualizar contador
                this.updateFavoriteCount();
                
                // Actualizar contador de favoritos en la tarjeta si existe
                const favoriteCountElement = document.querySelector(`[data-property-id="${propertyId}"].favorite-count`);
                if (favoriteCountElement && data.count !== undefined) {
                    favoriteCountElement.textContent = data.count;
                }
            } else {
                // Restaurar estado original
                button.innerHTML = originalContent;
                this.showNotification(data.error || 'Error al procesar la solicitud', 'error');
            }
        })
        .catch(error => {
            console.error('❌ Error de red:', error);
            button.innerHTML = originalContent;
            this.showNotification('Error de conexión', 'error');
        })
        .finally(() => {
            button.disabled = false;
        });
    }
    
    /**
     * Verificar si la página de favoritos está vacía
     */
    checkEmptyState() {
        const remainingCards = document.querySelectorAll('.property-card');
        if (remainingCards.length === 0) {
            setTimeout(() => {
                location.reload();
            }, 500);
        }
    }
    
    /**
     * Actualizar contador de favoritos en el header
     */
    updateFavoriteCount() {
        fetch('/favorites/total')
        .then(response => response.json())
        .then(data => {
            const favoriteCountElement = document.getElementById('favorite-count');
            if (favoriteCountElement) {
                const total = data.total || 0;
                favoriteCountElement.textContent = total;
                
                // Mostrar/ocultar badge según si hay favoritos
                if (total > 0) {
                    favoriteCountElement.classList.remove('hidden');
                } else {
                    favoriteCountElement.classList.add('hidden');
                }
            }
        })
        .catch(error => console.error('Error updating favorite count:', error));
    }
    
    /**
     * Limpiar todos los favoritos
     */
    clearFavorites() {
        if (!confirm('¿Estás seguro de que quieres eliminar todos tus favoritos? Esta acción no se puede deshacer.')) {
            return;
        }
        
        fetch('/favorites/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification(data.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                this.showNotification(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Error al limpiar favoritos', 'error');
        });
    }
    
    /**
     * Mostrar notificación
     */
    showNotification(message, type) {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        
        const notificationHtml = `
            <div class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="${icon} ${type === 'success' ? 'text-green-400' : 'text-red-400'}"></i>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                                <span class="sr-only">Cerrar</span>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remover notificaciones existentes
        const existingNotifications = document.querySelectorAll('.fixed.top-4.right-4');
        existingNotifications.forEach(notification => notification.remove());
        
        document.body.insertAdjacentHTML('beforeend', notificationHtml);
        
        // Auto-remover después de 3 segundos
        setTimeout(() => {
            const notification = document.querySelector('.fixed.top-4.right-4');
            if (notification) {
                notification.remove();
            }
        }, 3000);
    }
}

// Inicializar sistema de favoritos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.favoriteSystem = new FavoriteSystem();
});

// Función global para usar desde HTML
function toggleFavorite(propertyId) {
    if (window.favoriteSystem) {
        const button = event.target.closest('.favorite-btn');
        window.favoriteSystem.toggleFavorite(propertyId, button);
    }
}

function clearFavorites() {
    if (window.favoriteSystem) {
        window.favoriteSystem.clearFavorites();
    }
} 