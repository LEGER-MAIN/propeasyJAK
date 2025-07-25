<?php
/**
 * Vista: Todas las Actividades del Sistema
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */
?>

<!-- Header de la página -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-history text-primary"></i> 
                Todas las Actividades del Sistema
            </h2>
            <p class="text-muted mb-0">Registro completo de todas las actividades realizadas en el sistema</p>
        </div>
    </div>

<!-- Estadísticas de Actividades -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="fas fa-chart-bar text-primary"></i> 
            Estadísticas de Actividades
        </h5>
    </div>
    <div class="col-md-3">
        <div class="stats-card primary-gradient">
            <div class="stats-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="stats-content">
                <h3><?= number_format($stats['total_activities']) ?></h3>
                <p>Total Actividades</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card success-gradient">
            <div class="stats-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stats-content">
                <h3><?= number_format($stats['activities_today']) ?></h3>
                <p>Hoy</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card info-gradient">
            <div class="stats-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stats-content">
                <h3><?= number_format($stats['activities_this_week']) ?></h3>
                <p>Esta Semana</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card warning-gradient">
            <div class="stats-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stats-content">
                <h3><?= number_format($stats['activities_this_month']) ?></h3>
                <p>Este Mes</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="content-card mb-4">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="searchActivity" class="form-label">
                    <i class="fas fa-search"></i> Buscar Actividad
                </label>
                <input type="text" class="form-control" id="searchActivity" placeholder="Buscar por usuario, acción o descripción...">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="filterType" class="form-label">
                    <i class="fas fa-filter"></i> Tipo de Actividad
                </label>
                <select class="form-select" id="filterType">
                    <option value="">Todos los tipos</option>
                    <option value="login">Inicio de sesión</option>
                    <option value="logout">Cierre de sesión</option>
                    <option value="register">Registro</option>
                    <option value="create">Creación</option>
                    <option value="update">Actualización</option>
                    <option value="delete">Eliminación</option>
                    <option value="validate">Validación</option>
                    <option value="reject">Rechazo</option>
                    <option value="approve">Aprobación</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="filterUser" class="form-label">
                    <i class="fas fa-user"></i> Usuario
                </label>
                <select class="form-select" id="filterUser">
                    <option value="">Todos los usuarios</option>
                    <option value="admin">Administradores</option>
                    <option value="agente">Agentes</option>
                    <option value="cliente">Clientes</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Actividades -->
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="fas fa-list-alt text-primary"></i> 
            Actividades del Sistema
        </h5>
        <div class="text-muted">
            Mostrando <?= count($activities) ?> de <?= number_format($totalActivities) ?> actividades
        </div>
    </div>
    
    <?php if (!empty($activities)): ?>
        <div class="activities-list-full">
            <?php foreach ($activities as $activity): ?>
                <div class="activity-item-full">
                    <div class="activity-icon-full">
                        <i class="<?= $activity['icon'] ?>"></i>
                    </div>
                    <div class="activity-content-full">
                        <div class="activity-info">
                            <div class="activity-title-full"><?= htmlspecialchars($activity['action']) ?></div>
                            <div class="activity-description-full"><?= htmlspecialchars($activity['description']) ?></div>
                            <div class="activity-meta">
                                <div class="activity-user">
                                    <i class="fas fa-user"></i> 
                                    <?= htmlspecialchars($activity['user']['name']) ?>
                                    <span class="badge bg-<?= ($activity['user']['role'] ?? '') === 'admin' ? 'danger' : (($activity['user']['role'] ?? '') === 'agente' ? 'primary' : 'success') ?>">
                                        <?= ucfirst($activity['user']['role'] ?? 'usuario') ?>
                                    </span>
                                </div>
                                <div class="activity-time-full">
                                    <?= date('d/m/Y H:i:s', strtotime($activity['time'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Navegación de páginas" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">
                                <i class="fas fa-chevron-left"></i> Anterior
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">
                                Siguiente <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="fas fa-inbox fa-4x mb-3"></i>
            <h5>No hay actividades registradas</h5>
            <p>No se han encontrado actividades en el sistema.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Scripts para filtros -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchActivity');
    const filterType = document.getElementById('filterType');
    const filterUser = document.getElementById('filterUser');
    const activityItems = document.querySelectorAll('.activity-item-full');
    
    function filterActivities() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = filterType.value.toLowerCase();
        const selectedUser = filterUser.value.toLowerCase();
        
        activityItems.forEach(item => {
            const title = item.querySelector('.activity-title-full').textContent.toLowerCase();
            const description = item.querySelector('.activity-description-full').textContent.toLowerCase();
            const userRole = item.querySelector('.badge').textContent.toLowerCase();
            
            const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
            const matchesType = !selectedType || title.includes(selectedType);
            const matchesUser = !selectedUser || userRole.includes(selectedUser);
            
            if (matchesSearch && matchesType && matchesUser) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterActivities);
    filterType.addEventListener('change', filterActivities);
    filterUser.addEventListener('change', filterActivities);
});
</script>

<style>
/* Estilos profesionales para la página de actividades */


.activities-list-full {
    max-height: 500px;
    overflow-y: auto;
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
}

.activity-item-full {
    display: flex;
    align-items: center;
    padding: 0.875rem 1rem;
    border-bottom: 1px solid #f1f3f4;
    transition: all 0.2s ease;
    background: white;
}

.activity-item-full:hover {
    background-color: #f8f9fa;
    border-left: 3px solid var(--admin-primary);
}

.activity-item-full:last-child {
    border-bottom: none;
}

.activity-icon-full {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.875rem;
    flex-shrink: 0;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}

.activity-icon-full i {
    color: var(--admin-primary);
    font-size: 0.875rem;
}

.activity-content-full {
    flex: 1;
    min-width: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.activity-info {
    flex: 1;
    min-width: 0;
}

.activity-title-full {
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 0.125rem;
    font-size: 0.9rem;
    line-height: 1.3;
}

.activity-description-full {
    color: #6c757d;
    font-size: 0.8rem;
    line-height: 1.3;
    margin-bottom: 0.25rem;
}

.activity-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.activity-user {
    font-size: 0.75rem;
    color: #6c757d;
    display: flex;
    align-items: center;
}

.activity-user .badge {
    margin-left: 0.5rem;
    font-size: 0.65rem;
    padding: 0.125rem 0.375rem;
    border-radius: 8px;
    font-weight: 500;
}

.activity-time-full {
    font-size: 0.75rem;
    color: #6c757d;
    white-space: nowrap;
    background: #f8f9fa;
    padding: 0.125rem 0.375rem;
    border-radius: 3px;
    border: 1px solid #e9ecef;
}

/* Tarjetas de estadísticas compactas y profesionales */
.stats-card {
    background: white;
    color: #2c3e50;
    border-radius: 8px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--admin-primary);
}

.stats-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    border-color: var(--admin-primary);
}

.stats-card.success-gradient::before {
    background: #27ae60;
}

.stats-card.info-gradient::before {
    background: #3498db;
}

.stats-card.warning-gradient::before {
    background: #f39c12;
}

.stats-icon {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    border: 1px solid #e9ecef;
}

.stats-icon i {
    font-size: 1.1rem;
    color: var(--admin-primary);
}

.stats-card.success-gradient .stats-icon {
    background: #e8f5e8;
    border-color: #27ae60;
}

.stats-card.success-gradient .stats-icon i {
    color: #27ae60;
}

.stats-card.info-gradient .stats-icon {
    background: #e3f2fd;
    border-color: #3498db;
}

.stats-card.info-gradient .stats-icon i {
    color: #3498db;
}

.stats-card.warning-gradient .stats-icon {
    background: #fff3e0;
    border-color: #f39c12;
}

.stats-card.warning-gradient .stats-icon i {
    color: #f39c12;
}

.stats-content h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
}

.stats-content p {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Mejoras en filtros */
.content-card {
    background: white;
    border-radius: 8px;
    padding: 1.25rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    margin-bottom: 1.25rem;
    border: 1px solid #e9ecef;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 0.15rem rgba(52, 152, 219, 0.15);
}

.form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

/* Paginación mejorada */
.pagination .page-link {
    border-radius: 4px;
    margin: 0 1px;
    border: 1px solid #e9ecef;
    color: #6c757d;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.pagination .page-link:hover {
    background-color: var(--admin-primary);
    border-color: var(--admin-primary);
    color: white;
}

.pagination .page-item.active .page-link {
    background-color: var(--admin-primary);
    border-color: var(--admin-primary);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .activity-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .activity-time-full {
        margin-left: 0;
        margin-top: 0.5rem;
    }
    
    .stats-card {
        padding: 1.25rem;
    }
    
    .stats-content h3 {
        font-size: 1.5rem;
    }
}
</style>

 