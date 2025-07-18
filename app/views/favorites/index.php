<?php
/**
 * Vista: Listado de Propiedades Favoritas (Rediseño Moderno)
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Extraer datos de la vista
$favoritos = $data['favoritos'] ?? [];
$totalFavoritos = $data['totalFavoritos'] ?? 0;
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$estadisticas = $data['estadisticas'] ?? [];
$title = $data['title'] ?? 'Mis Favoritos';

// Establecer el título de la página para el layout
$pageTitle = $title;
?>

<!-- Contenido principal -->
<div class="min-h-screen" style="background-color: var(--bg-primary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header con breadcrumb -->
        <div class="mb-8">
            <nav class="flex items-center space-x-2 text-sm mb-4" style="color: var(--text-secondary);">
                <a href="/" class="transition-colors" style="color: var(--text-secondary);" onmouseover="this.style.color='var(--color-azul-marino)'" onmouseout="this.style.color='var(--text-secondary)'">Inicio</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="font-medium" style="color: var(--text-primary);">Mis Favoritos</span>
            </nav>
            
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold flex items-center gap-3" style="color: var(--text-primary);">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, var(--color-verde-esmeralda) 0%, var(--color-verde-esmeralda-hover) 100%);">
                            <i class="fas fa-heart text-white text-xl"></i>
                        </div>
                        Mis Propiedades Favoritas
                    </h1>
                    <p class="mt-2 text-lg" style="color: var(--text-secondary);">
                        <?php if ($totalFavoritos > 0): ?>
                            Tienes <span class="font-semibold" style="color: var(--color-azul-marino);"><?= $totalFavoritos ?></span> propiedad<?= $totalFavoritos != 1 ? 'es' : '' ?> guardada<?= $totalFavoritos != 1 ? 's' : '' ?> en tu lista
                        <?php else: ?>
                            Tu lista de favoritos está vacía
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="/properties" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium shadow-sm transition-all duration-200" style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white; border: 1px solid var(--color-azul-marino);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <i class="fas fa-search"></i>
                        Explorar Propiedades
                    </a>
                    <?php if ($totalFavoritos > 0): ?>
                    <button id="clearAllFavorites" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium shadow-sm transition-all duration-200" style="background-color: var(--color-gris-claro); color: var(--text-primary); border: 1px solid var(--color-gris-claro);" onmouseover="this.style.backgroundColor='var(--color-azul-marino-light)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'" onmouseout="this.style.backgroundColor='var(--color-gris-claro)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <i class="fas fa-trash-alt"></i>
                        Limpiar Todo
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Estadísticas mejoradas -->
        <?php if (!empty($estadisticas) && $totalFavoritos > 0): ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow" style="background-color: var(--bg-light); border-color: var(--color-gris-claro);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-secondary);">Total Favoritos</p>
                        <p class="text-2xl font-bold" style="color: var(--text-primary);"><?= $estadisticas['total_favoritos'] ?? 0 ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: rgba(42, 157, 143, 0.1);">
                        <i class="fas fa-heart" style="color: var(--color-verde-esmeralda);"></i>
                    </div>
                </div>
            </div>
            
            <div class="rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow" style="background-color: var(--bg-light); border-color: var(--color-gris-claro);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-secondary);">Ciudades</p>
                        <p class="text-2xl font-bold" style="color: var(--text-primary);"><?= $estadisticas['ciudades_diferentes'] ?? 0 ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: rgba(42, 157, 143, 0.1);">
                        <i class="fas fa-map-marker-alt" style="color: var(--color-verde-esmeralda);"></i>
                    </div>
                </div>
            </div>
            
            <div class="rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow" style="background-color: var(--bg-light); border-color: var(--color-gris-claro);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-secondary);">Tipos</p>
                        <p class="text-2xl font-bold" style="color: var(--text-primary);"><?= $estadisticas['tipos_diferentes'] ?? 0 ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: rgba(29, 53, 87, 0.1);">
                        <i class="fas fa-home" style="color: var(--color-azul-marino);"></i>
                    </div>
                </div>
            </div>
            
            <div class="rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow" style="background-color: var(--bg-light); border-color: var(--color-gris-claro);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--text-secondary);">Precio Promedio</p>
                        <p class="text-2xl font-bold" style="color: var(--text-primary);">$<?= number_format($estadisticas['precio_promedio'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: rgba(233, 196, 106, 0.1);">
                        <i class="fas fa-dollar-sign" style="color: var(--color-dorado-suave);"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Listado de favoritos -->
        <?php if (!empty($favoritos)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($favoritos as $favorito): ?>
            <div class="rounded-xl shadow-sm border overflow-hidden hover:shadow-lg transition-all duration-300 group" style="background-color: var(--bg-light); border-color: var(--color-gris-claro);">
                <!-- Imagen de la propiedad -->
                <div class="relative h-56 bg-gray-100 overflow-hidden">
                    <?php if (!empty($favorito['imagen_principal'])): ?>
                        <img src="<?= htmlspecialchars($favorito['imagen_principal']) ?>" 
                             alt="<?= htmlspecialchars($favorito['titulo']) ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                            <i class="fas fa-home text-4xl text-gray-400"></i>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Overlay con información -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    
                    <!-- Badges superiores -->
                    <div class="absolute top-3 left-3 flex gap-2">
                        <span class="text-white text-xs px-3 py-1 rounded-full font-medium shadow-lg" style="background-color: var(--color-azul-marino);">
                            <?= ucfirst($favorito['tipo']) ?>
                        </span>
                        <?php if ($favorito['estado_publicacion'] === 'activa'): ?>
                        <span class="text-white text-xs px-3 py-1 rounded-full font-medium shadow-lg" style="background-color: var(--color-verde-esmeralda);">
                            Disponible
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Botón eliminar -->
                    <button class="absolute top-3 right-3 w-8 h-8 backdrop-blur-sm rounded-full flex items-center justify-center transition-all duration-200 shadow-lg remove-favorite" 
                            style="background-color: rgba(255, 255, 255, 0.9); border: 1px solid var(--color-gris-claro); color: var(--text-secondary);"
                            onmouseover="this.style.backgroundColor='var(--color-gris-claro)'; this.style.color='var(--text-primary)'"
                            onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.9)'; this.style.color='var(--text-secondary)'"
                            data-favorito-id="<?= $favorito['favorito_id'] ?>" 
                            data-propiedad-titulo="<?= htmlspecialchars($favorito['titulo']) ?>"
                            title="Eliminar de favoritos">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                    
                    <!-- Precio -->
                    <div class="absolute bottom-3 left-3 right-3">
                        <div class="backdrop-blur-sm rounded-lg px-4 py-2 shadow-lg" style="background-color: rgba(255, 255, 255, 0.95);">
                            <div class="text-2xl font-bold" style="color: var(--text-primary);">$<?= number_format($favorito['precio'], 0, ',', '.') ?></div>
                            <div class="text-xs" style="color: var(--text-secondary);">Precio</div>
                        </div>
                    </div>
                </div>
                
                <!-- Contenido de la tarjeta -->
                <div class="p-6">
                    <!-- Título y ubicación -->
                    <a href="/properties/show/<?= $favorito['id'] ?>" class="block group">
                        <h3 class="text-lg font-bold transition-colors mb-2 line-clamp-2" style="color: var(--text-primary);" onmouseover="this.style.color='var(--color-azul-marino)'" onmouseout="this.style.color='var(--text-primary)'">
                            <?= htmlspecialchars($favorito['titulo']) ?>
                        </h3>
                    </a>
                    
                    <div class="flex items-center gap-2 mb-4" style="color: var(--text-secondary);">
                        <i class="fas fa-map-marker-alt" style="color: var(--color-azul-marino);"></i>
                        <span class="text-sm"><?= htmlspecialchars($favorito['ciudad']) ?><?= !empty($favorito['sector']) ? ', ' . htmlspecialchars($favorito['sector']) : '' ?></span>
                    </div>
                    
                    <!-- Características -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-lg font-bold" style="color: var(--text-primary);"><?= $favorito['habitaciones'] ?? 'N/A' ?></div>
                            <div class="text-xs" style="color: var(--text-secondary);">Habitaciones</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold" style="color: var(--text-primary);"><?= $favorito['banos'] ?? 'N/A' ?></div>
                            <div class="text-xs" style="color: var(--text-secondary);">Baños</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold" style="color: var(--text-primary);"><?= $favorito['metros_cuadrados'] ?? 'N/A' ?> m²</div>
                            <div class="text-xs" style="color: var(--text-secondary);">Área</div>
                        </div>
                    </div>
                    
                    <!-- Agente -->
                    <?php if (!empty($favorito['agente_nombre'])): ?>
                    <div class="flex items-center gap-3 p-3 rounded-lg mb-4" style="background-color: var(--bg-secondary);">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: rgba(29, 53, 87, 0.1);">
                            <i class="fas fa-user-tie text-sm" style="color: var(--color-azul-marino);"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium" style="color: var(--text-primary);">
                                <?= htmlspecialchars($favorito['agente_nombre'] . ' ' . $favorito['agente_apellido']) ?>
                            </div>
                            <div class="text-xs" style="color: var(--text-secondary);">Agente Inmobiliario</div>
                        </div>
                        <?php if (!empty($favorito['agente_telefono'])): ?>
                        <a href="tel:<?= $favorito['agente_telefono'] ?>" 
                           class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                           style="background-color: rgba(42, 157, 143, 0.1);"
                           onmouseover="this.style.backgroundColor='var(--color-verde-esmeralda-light)'"
                           onmouseout="this.style.backgroundColor='rgba(42, 157, 143, 0.1)'"
                           title="Llamar al agente">
                            <i class="fas fa-phone text-sm" style="color: var(--color-verde-esmeralda);"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Fecha de agregado -->
                    <div class="flex items-center gap-2 text-xs mb-4" style="color: var(--text-muted);">
                        <i class="fas fa-calendar-plus"></i>
                        <span>Agregado el <?= date('d/m/Y', strtotime($favorito['fecha_agregado'])) ?></span>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="flex gap-2">
                        <a href="/properties/show/<?= $favorito['id'] ?>" 
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-medium transition-all duration-200 text-center"
                           style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white;"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <i class="fas fa-eye"></i>
                            Ver Detalles
                        </a>
                        <button class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-200 remove-favorite"
                                style="background-color: var(--color-gris-claro); color: var(--text-secondary);"
                                onmouseover="this.style.backgroundColor='var(--color-azul-marino-light)'; this.style.color='var(--text-primary)'; this.style.transform='translateY(-2px)'"
                                onmouseout="this.style.backgroundColor='var(--color-gris-claro)'; this.style.color='var(--text-secondary)'; this.style.transform='translateY(0)'"
                                data-favorito-id="<?= $favorito['favorito_id'] ?>"
                                data-propiedad-titulo="<?= htmlspecialchars($favorito['titulo']) ?>"
                                title="Eliminar de favoritos">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginación mejorada -->
        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center mt-12">
            <nav class="inline-flex items-center gap-1 bg-white rounded-lg shadow-sm border border-gray-200 p-1">
                <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" 
                   class="px-3 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="px-3 py-2 rounded-md font-medium transition-colors <?= $i == $currentPage ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" 
                   class="px-3 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <!-- Estado vacío mejorado -->
        <div class="flex flex-col items-center justify-center py-20">
            <div class="w-24 h-24 bg-gradient-to-br from-red-100 to-pink-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-heart-broken text-3xl text-red-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No tienes propiedades favoritas</h3>
            <p class="text-gray-600 text-center max-w-md mb-8">
                Explora nuestras propiedades y agrega las que más te gusten a tu lista de favoritos para tenerlas siempre a mano.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="/properties" 
                   class="inline-flex items-center gap-2 px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium shadow-lg transition-all duration-200 hover:shadow-xl">
                    <i class="fas fa-search"></i>
                    Explorar Propiedades
                </a>
                <a href="/" 
                   class="inline-flex items-center gap-2 px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                    <i class="fas fa-home"></i>
                    Ir al Inicio
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de confirmación mejorado -->
<div id="modal-backdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-heart-broken text-red-500 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Eliminar de Favoritos</h2>
                    <p class="text-sm text-gray-500">Esta acción no se puede deshacer</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-6">
                ¿Estás seguro de que quieres eliminar <strong id="modal-propiedad-titulo"></strong> de tus favoritos?
            </p>
            
            <div class="flex gap-3">
                <button id="cancelRemoveBtn" 
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                    Cancelar
                </button>
                <button id="confirmRemoveBtn" 
                        class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para limpiar todo -->
<div id="clearAllModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto transform transition-all">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Limpiar Todos los Favoritos</h2>
                    <p class="text-sm text-gray-500">Esta acción es irreversible</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-6">
                ¿Estás seguro de que quieres eliminar <strong>todas</strong> las propiedades de tu lista de favoritos? Esta acción no se puede deshacer.
            </p>
            
            <div class="flex gap-3">
                <button id="cancelClearAllBtn" 
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                    Cancelar
                </button>
                <button id="confirmClearAllBtn" 
                        class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Limpiar Todo
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let favoritoIdToRemove = null;
let propiedadTituloToRemove = '';

// Elementos del DOM
const modalBackdrop = document.getElementById('modal-backdrop');
const clearAllModal = document.getElementById('clearAllModal');
const confirmRemoveBtn = document.getElementById('confirmRemoveBtn');
const cancelRemoveBtn = document.getElementById('cancelRemoveBtn');
const confirmClearAllBtn = document.getElementById('confirmClearAllBtn');
const cancelClearAllBtn = document.getElementById('cancelClearAllBtn');
const clearAllFavoritesBtn = document.getElementById('clearAllFavorites');

// Funciones del modal
function showModal(favoritoId, propiedadTitulo) {
    favoritoIdToRemove = favoritoId;
    propiedadTituloToRemove = propiedadTitulo;
    document.getElementById('modal-propiedad-titulo').textContent = propiedadTitulo;
    modalBackdrop.classList.remove('hidden');
}

function hideModal() {
    favoritoIdToRemove = null;
    propiedadTituloToRemove = '';
    modalBackdrop.classList.add('hidden');
}

function showClearAllModal() {
    clearAllModal.classList.remove('hidden');
}

function hideClearAllModal() {
    clearAllModal.classList.add('hidden');
}

// Event listeners para botones de eliminar
document.addEventListener('DOMContentLoaded', function() {
    // Botones de eliminar individual
    document.querySelectorAll('.remove-favorite').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const favoritoId = this.dataset.favoritoId;
            const propiedadTitulo = this.dataset.propiedadTitulo;
            showModal(favoritoId, propiedadTitulo);
        });
    });
    
    // Botón de limpiar todo
    if (clearAllFavoritesBtn) {
        clearAllFavoritesBtn.addEventListener('click', showClearAllModal);
    }
    
    // Botones del modal individual
    confirmRemoveBtn.addEventListener('click', function() {
        if (favoritoIdToRemove) {
            removeFavoriteById(favoritoIdToRemove);
            hideModal();
        }
    });
    
    cancelRemoveBtn.addEventListener('click', hideModal);
    modalBackdrop.addEventListener('click', function(e) {
        if (e.target === modalBackdrop) hideModal();
    });
    
    // Botones del modal de limpiar todo
    confirmClearAllBtn.addEventListener('click', function() {
        clearAllFavorites();
        hideClearAllModal();
    });
    
    cancelClearAllBtn.addEventListener('click', hideClearAllModal);
    clearAllModal.addEventListener('click', function(e) {
        if (e.target === clearAllModal) hideClearAllModal();
    });
});

// Función para eliminar favorito individual
function removeFavoriteById(favoritoId) {
    const formData = new FormData();
    formData.append('favorito_id', favoritoId);
    
    fetch('/favorites/eliminar-por-id', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            updateFavoriteCounter(data.total_favoritos);
            
            // Remover la tarjeta de la propiedad con animación
            const card = document.querySelector(`[data-favorito-id="${favoritoId}"]`).closest('.bg-white');
            card.style.transition = 'all 0.3s ease';
            card.style.transform = 'scale(0.95)';
            card.style.opacity = '0';
            
            setTimeout(() => {
                card.remove();
                
                // Si no quedan favoritos, recargar la página
                const remainingCards = document.querySelectorAll('.bg-white.rounded-xl');
                if (remainingCards.length === 0) {
                    setTimeout(() => window.location.reload(), 500);
                }
            }, 300);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al eliminar el favorito', 'error');
    });
}

// Función para limpiar todos los favoritos
function clearAllFavorites() {
    fetch('/favorites/limpiar-todos', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            updateFavoriteCounter(0);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al limpiar los favoritos', 'error');
    });
}

// Función para mostrar notificaciones
function showNotification(message, type) {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-6 right-6 z-50 px-6 py-4 rounded-xl shadow-2xl text-white flex items-center gap-3 ${colors[type]} transform translate-x-full transition-transform duration-300`;
    notification.innerHTML = `
        <i class="fas ${icons[type]} text-xl"></i>
        <span class="font-medium">${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remover después de 4 segundos
    setTimeout(() => {
        notification.style.transform = 'translateX(full)';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Función para actualizar contador de favoritos en el header
function updateFavoriteCounter(total) {
    const counters = document.querySelectorAll('.favorite-counter');
    counters.forEach(counter => {
        if (total > 0) {
            counter.textContent = total > 99 ? '99+' : total;
            counter.style.display = 'inline-flex';
        } else {
            counter.style.display = 'none';
        }
    });
}
</script> 