/**
 * JavaScript para el Sistema de Favoritos
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este archivo maneja todas las interacciones AJAX relacionadas con favoritos:
 * toggle, agregar, eliminar, contador, notificaciones.
 */

class FavoritesManager {
    constructor() {
        
        this.init();
    }
    
    /**
     * Inicializar el sistema de favoritos
     */
    init() {
        this.bindEvents();
        this.loadFavoriteStates();
    }
    
    /**
     * Vincular eventos a los botones de favoritos
     */
    bindEvents() {
        // Event listeners para botones de toggle de favoritos
        document.addEventListener('click', (e) => {
            if (e.target.closest('.favorite-toggle')) {
                e.preventDefault();
                const button = e.target.closest('.favorite-toggle');
                const propiedadId = button.dataset.propiedadId;
                this.toggleFavorite(propiedadId, button);
            }
        });
        
        // Event listeners para botones de agregar favorito
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-favorite')) {
                e.preventDefault();
                const button = e.target.closest('.add-favorite');
                const propiedadId = button.dataset.propiedadId;
                this.addFavorite(propiedadId, button);
            }
        });
        
        // Event listeners para botones de eliminar favorito
        document.addEventListener('click', (e) => {
            if (e.target.closest('.remove-favorite')) {
                e.preventDefault();
                const button = e.target.closest('.remove-favorite');
                const propiedadId = button.dataset.propiedadId;
                this.removeFavorite(propiedadId, button);
            }
        });
    }
    
    /**
     * Cargar estados de favoritos para todas las propiedades en la página
     */
    loadFavoriteStates() {
        const favoriteButtons = document.querySelectorAll('[data-propiedad-id]');
        if (favoriteButtons.length === 0) return;
        
        const propiedadIds = Array.from(favoriteButtons).map(btn => btn.dataset.propiedadId);
        
        // Verificar estados de favoritos en lote
        this.checkFavoriteStates(propiedadIds);
    }
    
    /**
     * Verificar estados de favoritos para múltiples propiedades
     */
    async checkFavoriteStates(propiedadIds) {
        try {
            const promises = propiedadIds.map(id => this.checkFavoriteState(id));
            await Promise.all(promises);
        } catch (error) {
            console.error('Error al verificar estados de favoritos:', error);
        }
    }
    
    /**
     * Verificar estado de favorito para una propiedad específica
     */
    async checkFavoriteState(propiedadId) {
        try {
            const response = await fetch(`/favorites/verificar?propiedad_id=${propiedadId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateFavoriteButton(propiedadId, data.is_favorite);
            }
        } catch (error) {
            console.error(`Error al verificar favorito para propiedad ${propiedadId}:`, error);
        }
    }
    
    /**
     * Toggle de favorito (agregar/eliminar)
     */
    async toggleFavorite(propiedadId, button) {

        
        if (!this.isUserAuthenticated()) {
            this.showLoginPrompt();
            return;
        }
        
        // Mostrar estado de carga
        this.showLoadingState(button);
        
        try {
            const formData = new FormData();
            formData.append('propiedad_id', propiedadId);
            

            
            const response = await fetch('/favorites/toggle', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Actualizar estado del botón
                this.updateFavoriteButton(propiedadId, data.is_favorite);
                
                // Actualizar contador en el header
                this.updateFavoriteCounter(data.total_favoritos);
                
                // Mostrar notificación
                this.showNotification(data.message, 'success');
                
                // Animación de corazón
                this.animateHeart(button, data.is_favorite);
                
            } else {
                this.showNotification(data.message, 'error');
                this.hideLoadingState(button);
            }
            
        } catch (error) {
            console.error('Error al toggle favorito:', error);
            this.showNotification('Error al procesar la solicitud', 'error');
            this.hideLoadingState(button);
        }
    }
    
    /**
     * Agregar propiedad a favoritos
     */
    async addFavorite(propiedadId, button) {
        if (!this.isUserAuthenticated()) {
            this.showLoginPrompt();
            return;
        }
        
        this.showLoadingState(button);
        
        try {
            const formData = new FormData();
            formData.append('propiedad_id', propiedadId);
            
            const response = await fetch('/favorites/agregar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateFavoriteButton(propiedadId, true);
                this.updateFavoriteCounter(data.total_favoritos);
                this.showNotification(data.message, 'success');
                this.animateHeart(button, true);
            } else {
                this.showNotification(data.message, 'error');
                this.hideLoadingState(button);
            }
            
        } catch (error) {
            console.error('Error al agregar favorito:', error);
            this.showNotification('Error al agregar a favoritos', 'error');
            this.hideLoadingState(button);
        }
    }
    
    /**
     * Eliminar propiedad de favoritos
     */
    async removeFavorite(propiedadId, button) {
        if (!this.isUserAuthenticated()) {
            this.showLoginPrompt();
            return;
        }
        
        this.showLoadingState(button);
        
        try {
            const formData = new FormData();
            formData.append('propiedad_id', propiedadId);
            
            const response = await fetch('/favorites/eliminar', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.updateFavoriteButton(propiedadId, false);
                this.updateFavoriteCounter(data.total_favoritos);
                this.showNotification(data.message, 'success');
                this.animateHeart(button, false);
            } else {
                this.showNotification(data.message, 'error');
                this.hideLoadingState(button);
            }
            
        } catch (error) {
            console.error('Error al eliminar favorito:', error);
            this.showNotification('Error al eliminar de favoritos', 'error');
            this.hideLoadingState(button);
        }
    }
    
    /**
     * Actualizar estado visual del botón de favorito
     */
    updateFavoriteButton(propiedadId, isFavorite) {
        const buttons = document.querySelectorAll(`[data-propiedad-id="${propiedadId}"]`);
        
        buttons.forEach(button => {
            const icon = button.querySelector('i');
            const text = button.querySelector('span');
            
            // Determinar si es botón de listado (sin texto) o detalle (con texto)
            const isListButton = !text;
            
            if (isFavorite) {
                // Estado favorito (verde esmeralda sólido con texto blanco)
                if (isListButton) {
                    button.className = 'favorite-toggle rounded-full p-2 transition-all duration-200 hover:transform hover:scale-105';
                    button.style.cssText = 'background-color: var(--color-verde-esmeralda); color: var(--text-light); border: 2px solid var(--color-verde-esmeralda); box-shadow: 0 2px 4px rgba(42, 157, 143, 0.3);';
                    if (icon) icon.className = 'fas fa-heart text-sm';
                } else {
                    button.className = 'favorite-toggle rounded-md px-4 py-2 transition-all duration-200 hover:transform hover:scale-105 flex items-center';
                    button.style.cssText = 'background-color: var(--color-verde-esmeralda); color: var(--text-light); border: 2px solid var(--color-verde-esmeralda); box-shadow: 0 2px 4px rgba(42, 157, 143, 0.3);';
                    if (icon) icon.className = 'fas fa-heart mr-2';
                    if (text) {
                        text.textContent = 'En Favoritos';
                        text.style.color = 'var(--text-light)';
                    }
                }
                button.title = 'Eliminar de favoritos';
            } else {
                // Estado no favorito (contorno verde esmeralda)
                if (isListButton) {
                    button.className = 'favorite-toggle rounded-full p-2 transition-all duration-200 hover:transform hover:scale-105';
                    button.style.cssText = 'background-color: var(--bg-light); color: var(--color-verde-esmeralda); border: 2px solid var(--color-verde-esmeralda);';
                    if (icon) icon.className = 'far fa-heart text-sm';
                } else {
                    button.className = 'favorite-toggle rounded-md px-4 py-2 transition-all duration-200 hover:transform hover:scale-105 flex items-center';
                    button.style.cssText = 'background-color: var(--bg-light); color: var(--color-verde-esmeralda); border: 2px solid var(--color-verde-esmeralda);';
                    if (icon) icon.className = 'far fa-heart mr-2';
                    if (text) {
                        text.textContent = 'Agregar a Favoritos';
                        text.style.color = 'var(--color-verde-esmeralda)';
                    }
                }
                button.title = 'Agregar a favoritos';
            }
            
            this.hideLoadingState(button);
        });
    }
    
    /**
     * Mostrar estado de carga en el botón
     */
    showLoadingState(button) {
        const icon = button.querySelector('i');
        const originalIcon = icon.className;
        
        button.disabled = true;
        icon.className = 'fas fa-spinner fa-spin';
        
        // Guardar el icono original para restaurarlo después
        button.dataset.originalIcon = originalIcon;
    }
    
    /**
     * Ocultar estado de carga en el botón
     */
    hideLoadingState(button) {
        const icon = button.querySelector('i');
        const originalIcon = button.dataset.originalIcon;
        
        button.disabled = false;
        if (originalIcon) {
            icon.className = originalIcon;
            delete button.dataset.originalIcon;
        }
    }
    
    /**
     * Actualizar contador de favoritos en el header
     */
    updateFavoriteCounter(total) {
        const counterElements = document.querySelectorAll('.favorite-counter');
        
        counterElements.forEach(element => {
            element.textContent = total;
            
            if (total > 0) {
                element.style.display = 'inline';
                element.classList.add('animate__animated', 'animate__pulse');
                
                // Remover animación después de completarse
                setTimeout(() => {
                    element.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            } else {
                element.style.display = 'none';
            }
        });
    }
    
    /**
     * Animación del corazón al toggle
     */
    animateHeart(button, isFavorite) {
        const icon = button.querySelector('i');
        
        if (isFavorite) {
            // Animación de agregar
            icon.classList.add('animate__animated', 'animate__heartBeat');
        } else {
            // Animación de eliminar
            icon.classList.add('animate__animated', 'animate__fadeOut');
        }
        
        // Remover clases de animación después de completarse
        setTimeout(() => {
            icon.classList.remove('animate__animated', 'animate__heartBeat', 'animate__fadeOut');
        }, 1000);
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    isUserAuthenticated() {
        const hasUserClass = document.body.classList.contains('user-authenticated');
        const hasUserId = document.querySelector('[data-user-id]') !== null;
        
        // Verificar si existe un elemento que indique que el usuario está logueado
        return hasUserClass || hasUserId;
    }
    
    /**
     * Mostrar prompt de login
     */
    showLoginPrompt() {
        const modal = new bootstrap.Modal(document.getElementById('loginModal'));
        modal.show();
        
        this.showNotification('Debes iniciar sesión para usar favoritos', 'warning');
    }
    
    /**
     * Mostrar notificación
     */
    showNotification(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        const icon = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        }[type] || 'fas fa-info-circle';
        
        // Crear elemento de notificación
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
        alert.innerHTML = `
            <i class="${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Agregar al DOM
        document.body.appendChild(alert);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
        
        // Event listener para cerrar manualmente
        alert.querySelector('.btn-close').addEventListener('click', () => {
            alert.remove();
        });
    }
    
    /**
     * Obtener contador de favoritos del servidor
     */
    async getFavoriteCount() {
        try {
            const response = await fetch('/favorites/contador', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.updateFavoriteCounter(data.total_favoritos);
            }
        } catch (error) {
            console.error('Error al obtener contador de favoritos:', error);
        }
    }
}

// Inicializar el sistema de favoritos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.favoritesManager = new FavoritesManager();
    
    // Cargar contador de favoritos al cargar la página
    if (window.favoritesManager.isUserAuthenticated()) {
        window.favoritesManager.getFavoriteCount();
    }
});

// Exportar para uso global
window.FavoritesManager = FavoritesManager; 