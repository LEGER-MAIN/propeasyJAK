<?php include APP_PATH . '/views/layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">
                    <i class="fas fa-heart text-danger"></i>
                    Mis Favoritos
                </h1>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-3">
                        <i class="fas fa-home"></i>
                        <?php echo $totalFavoritos; ?> propiedades
                    </span>
                    <?php if ($totalFavoritos > 0): ?>
                        <button class="btn btn-outline-danger btn-sm" onclick="limpiarFavoritos()">
                            <i class="fas fa-trash"></i>
                            Limpiar Todo
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($favoritos)): ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-heart text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-muted">No tienes favoritos aún</h3>
                    <p class="text-muted">Explora las propiedades disponibles y agrega las que más te gusten a tus favoritos.</p>
                    <a href="/properties" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Explorar Propiedades
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($favoritos as $favorito): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 property-card">
                                <div class="position-relative">
                                    <?php if ($favorito['imagen_principal']): ?>
                                        <img src="/uploads/properties/<?php echo htmlspecialchars($favorito['imagen_principal']); ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($favorito['titulo']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="fas fa-home text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Botón de favorito -->
                                    <button class="favorite-btn absolute top-2 right-2 bg-white bg-opacity-90 hover:bg-opacity-100 p-2 rounded-full shadow-md transition-all duration-200 hover:scale-110 active" 
                                            data-property-id="<?php echo $favorito['id']; ?>"
                                            data-is-favorite="true"
                                            onclick="toggleFavorite(<?php echo $favorito['id']; ?>)">
                                        <i class="fas fa-heart text-red-500"></i>
                                    </button>
                                    
                                    <!-- Badge de estado -->
                                    <span class="badge bg-success position-absolute top-0 start-0 m-2">
                                        <?php echo ucfirst($favorito['estado']); ?>
                                    </span>
                                </div>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($favorito['titulo']); ?></h5>
                                    <p class="card-text text-muted">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($favorito['ciudad'] . ', ' . $favorito['sector']); ?>
                                    </p>
                                    
                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <small class="text-muted">Habitaciones</small>
                                            <div><i class="fas fa-bed"></i> <?php echo $favorito['habitaciones']; ?></div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Baños</small>
                                            <div><i class="fas fa-bath"></i> <?php echo $favorito['banos']; ?></div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">m²</small>
                                            <div><i class="fas fa-ruler-combined"></i> <?php echo $favorito['metros_cuadrados']; ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="text-primary mb-0">
                                            $<?php echo number_format($favorito['precio'], 0, ',', '.'); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-heart text-danger"></i>
                                            <?php echo $favorito['total_favoritos']; ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i>
                                            <?php echo htmlspecialchars($favorito['nombre_agente'] . ' ' . $favorito['apellido_agente']); ?>
                                        </small>
                                        <a href="/properties/show/<?php echo $favorito['id']; ?>" class="btn btn-primary btn-sm">
                                            Ver Detalles
                                        </a>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-clock"></i>
                                        Agregado el <?php echo date('d/m/Y', strtotime($favorito['fecha_favorito'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Paginación de favoritos">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Función para alternar favorito
function toggleFavorite(propiedadId) {
    const btn = event.target.closest('.favorite-btn');
    const isFavorite = btn.classList.contains('active');
    
    const url = isFavorite ? '/favorites/remove' : '/favorites/add';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            property_id: propiedadId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (isFavorite) {
                // Remover de favoritos
                btn.classList.remove('active');
                btn.innerHTML = '<i class="far fa-heart"></i>';
                
                // Si estamos en la página de favoritos, remover la tarjeta
                if (window.location.pathname === '/favorites') {
                    btn.closest('.col-md-6').remove();
                    
                    // Verificar si no quedan más favoritos
                    const remainingCards = document.querySelectorAll('.property-card');
                    if (remainingCards.length === 0) {
                        location.reload(); // Recargar para mostrar mensaje de "no hay favoritos"
                    }
                }
            } else {
                // Agregar a favoritos
                btn.classList.add('active');
                btn.innerHTML = '<i class="fas fa-heart text-danger"></i>';
            }
            
            // Mostrar notificación
            showNotification(data.message, 'success');
            
            // Actualizar contador de favoritos en el header si existe
            updateFavoriteCount();
        } else {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al procesar la solicitud', 'error');
    });
}

// Función para limpiar todos los favoritos
function clearFavorites() {
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
            showNotification(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.error || 'Error al limpiar favoritos', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al limpiar favoritos', 'error');
    });
}

// Función para mostrar notificaciones
function showNotification(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 3000);
}

// Función para actualizar contador de favoritos en el header
function updateFavoriteCount() {
    fetch('/favorites/total')
    .then(response => response.json())
    .then(data => {
        const favoriteCountElement = document.getElementById('favorite-count');
        if (favoriteCountElement) {
            favoriteCountElement.textContent = data.total;
        }
    })
    .catch(error => console.error('Error updating favorite count:', error));
}

// Actualizar contador al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateFavoriteCount();
});
</script>

<?php include APP_PATH . '/views/layouts/footer.php'; ?> 