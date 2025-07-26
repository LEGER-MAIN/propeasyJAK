<?php
/**
 * Vista de Listado de Agentes con Perfiles Públicos
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Asegurar que las variables estén definidas
$agentes = $agentes ?? [];
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$ciudad = $ciudad ?? '';
?>

<style>
.agentes-header {
    background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    margin-top: 0;
}

.agentes-header h1 {
    text-align: center;
    margin-bottom: 1rem;
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
}

.agentes-header p {
    text-align: center;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
}

.filtros {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.filtros-form {
    display: flex;
    gap: 1rem;
    align-items: end;
    justify-content: center;
    max-width: 800px;
    margin: 0 auto;
    flex-wrap: wrap;
}

.filtros-form .form-group {
    flex: 1;
    min-width: 200px;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
}

.btn-filtrar {
    background: var(--color-azul-marino);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-filtrar:hover {
    background: var(--color-azul-marino-hover);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(29, 53, 87, 0.3);
}

.main-content-agentes {
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-top: 0;
    padding-top: 0;
}

/* Eliminar márgenes extra */
.max-w-7xl {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.agentes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.agente-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.agente-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.agente-header {
    background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%);
    color: white;
    padding: 2rem;
    text-align: center;
    position: relative;
}

.foto-agente {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    margin-bottom: 1rem;
}

.foto-agente-default {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    border: 4px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.nombre-agente {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.ubicacion-agente {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 1rem;
}

.experiencia-agente {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    display: inline-block;
}

.licencia-agente {
    background: rgba(255,255,255,0.15);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    display: inline-block;
    margin-top: 0.5rem;
}

.agente-body {
    padding: 1.5rem;
}

.especialidades {
    margin-bottom: 1.5rem;
}

.especialidades-titulo {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.especialidades-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.especialidad-tag {
    background: rgba(29, 53, 87, 0.1);
    color: var(--color-azul-marino);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.descripcion-agente {
    margin-bottom: 1rem;
}

.descripcion-agente p {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.4;
    margin: 0;
}

.idiomas-agente {
    margin-bottom: 1rem;
}

.idiomas-titulo {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.idiomas-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.idioma-tag {
    background: #e8f4fd;
    color: #2196f3;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.estadisticas-agente {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.estadistica-item {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.estadistica-numero {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-azul-marino);
    margin-bottom: 0.25rem;
}

.estadistica-label {
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.calificacion-agente {
    text-align: center;
    margin-bottom: 1rem;
}

.calificacion-estrellas {
    color: #ffc107;
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.calificacion-numero {
    color: #333;
    font-weight: 600;
    margin-left: 0.5rem;
}

.calificacion-total {
    font-size: 0.8rem;
    color: #666;
}

.info-adicional-agente {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.info-badge {
    background: rgba(255,255,255,0.15);
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    max-width: fit-content;
}

.info-badge.online-status {
    background: rgba(76, 175, 80, 0.2);
}

.info-badge.online-status i {
    color: #4caf50;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.agente-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #eee;
}

.horario-atencion {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    padding: 0.5rem;
    background: white;
    border-radius: 8px;
    border-left: 3px solid var(--color-azul-marino);
}

.horario-atencion i {
    color: var(--color-azul-marino);
}

.acciones-agente {
    display: flex;
    gap: 0.5rem;
}

.btn-ver-perfil {
    background: var(--color-azul-marino);
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: all 0.3s ease;
    flex: 1;
}

.btn-ver-perfil:hover {
    background: var(--color-azul-marino-hover);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(29, 53, 87, 0.3);
}

.btn-contactar-agente {
    background: var(--color-verde-esmeralda);
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: all 0.3s ease;
    flex: 1;
}

.btn-contactar-agente:hover {
    background: var(--color-verde-esmeralda-hover);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(42, 157, 143, 0.3);
}

.btn-ver-perfil {
    background: var(--color-azul-marino);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    width: 100%;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: all 0.3s ease;
}

.btn-ver-perfil:hover {
    background: var(--color-azul-marino-hover);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(29, 53, 87, 0.3);
}

.paginacion {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
}

.pagina-item {
    padding: 0.5rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 5px;
    text-decoration: none;
    color: var(--color-azul-marino);
    transition: all 0.3s ease;
}

.pagina-item:hover {
    background: var(--color-azul-marino);
    color: white;
    border-color: var(--color-azul-marino);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(29, 53, 87, 0.3);
}

.pagina-activa {
    background: var(--color-azul-marino);
    color: white;
    border-color: var(--color-azul-marino);
}

.sin-resultados {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.sin-resultados i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.mt-3 {
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .agentes-header {
        padding: 2rem 0;
    }
    
    .filtros {
        max-width: 95%;
        margin-left: auto;
        margin-right: auto;
        padding: 1rem;
    }
    
    .filtros-form {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .filtros-form .form-group {
        min-width: auto;
        width: 100%;
    }
    
    .agentes-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .estadisticas-agente {
        grid-template-columns: 1fr;
    }
    
    .paginacion {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .filtros {
        max-width: 100%;
        margin: 0 0.5rem 2rem 0.5rem;
        border-radius: 8px;
    }
    
    .filtros-form {
        gap: 0.5rem;
    }
    
    .form-control {
        padding: 0.6rem;
        font-size: 0.9rem;
    }
    
    .btn-filtrar {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }
}
</style>

<!-- Contenido específico de la página -->
<div class="agentes-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1>Agentes Inmobiliarios</h1>
        <p>Conoce a nuestros profesionales especializados en bienes raíces</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 main-content-agentes">
    <!-- Filtros -->
    <div class="filtros">
        <form class="filtros-form" method="GET">
            <div class="form-group">
                <label for="ciudad">Ciudad</label>
                <input type="text" class="form-control" id="ciudad" name="ciudad" 
                       value="<?= htmlspecialchars($ciudad) ?>" placeholder="Filtrar por ciudad...">
            </div>
            <div class="form-group">
                <label for="ordenar">Ordenar por</label>
                <select class="form-control" id="ordenar" name="ordenar">
                    <option value="nombre" <?= ($_GET['ordenar'] ?? '') == 'nombre' ? 'selected' : '' ?>>Nombre</option>
                    <option value="experiencia" <?= ($_GET['ordenar'] ?? '') == 'experiencia' ? 'selected' : '' ?>>Experiencia</option>
                    <option value="propiedades" <?= ($_GET['ordenar'] ?? '') == 'propiedades' ? 'selected' : '' ?>>Más propiedades</option>
                    <option value="calificacion" <?= ($_GET['ordenar'] ?? '') == 'calificacion' ? 'selected' : '' ?>>Mejor calificación</option>
                    <option value="reciente" <?= ($_GET['ordenar'] ?? '') == 'reciente' ? 'selected' : '' ?>>Más reciente</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-filtrar">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
            </div>
        </form>
    </div>
    
    <!-- Listado de Agentes -->
    <?php if (!empty($agentes)): ?>
        <div class="agentes-grid">
            <?php foreach ($agentes as $agente): ?>
            <div class="agente-card">
                <div class="agente-header">
                    <?php if (!empty($agente['foto_perfil'])): ?>
                        <img src="<?= htmlspecialchars($agente['foto_perfil']) ?>" alt="Foto de perfil" class="foto-agente">
                    <?php else: ?>
                        <div class="foto-agente-default">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    <h3 class="nombre-agente"><?= htmlspecialchars(($agente['nombre'] ?? '') . ' ' . ($agente['apellido'] ?? '')) ?></h3>
                    <?php if (!empty($agente['ciudad'])): ?>
                        <p class="ubicacion-agente">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($agente['ciudad']) ?>
                            <?= !empty($agente['sector']) ? ', ' . htmlspecialchars($agente['sector']) : '' ?>
                        </p>
                    <?php endif; ?>
                    
                    <div class="info-adicional-agente">
                        <?php if (!empty($agente['licencia_inmobiliaria'])): ?>
                            <span class="info-badge">
                                <i class="fas fa-certificate"></i>
                                Lic. <?= htmlspecialchars($agente['licencia_inmobiliaria']) ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($agente['tiempo_registro'])): ?>
                            <span class="info-badge">
                                <i class="fas fa-calendar-alt"></i>
                                Miembro desde <?= $agente['tiempo_registro'] ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($agente['ultimo_acceso_hace'])): ?>
                            <span class="info-badge online-status">
                                <i class="fas fa-circle"></i>
                                Activo hace <?= $agente['ultimo_acceso_hace'] ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($agente['experiencia_anos'])): ?>
                        <span class="experiencia-agente">
                            <i class="fas fa-clock"></i>
                            <?= $agente['experiencia_anos'] ?> años de experiencia
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($agente['licencia_inmobiliaria'])): ?>
                        <span class="licencia-agente">
                            <i class="fas fa-certificate"></i>
                            Lic. <?= htmlspecialchars($agente['licencia_inmobiliaria']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="agente-body">
                    <?php 
                    $especialidades = $agente['especialidades'] ?? [];
                    $especialidadesValidas = [];
                    if (!empty($especialidades)) {
                        if (is_array($especialidades)) {
                            $especialidadesValidas = array_filter(array_map('trim', $especialidades));
                        } else {
                            $especialidadesArray = explode(',', $especialidades);
                            $especialidadesValidas = array_filter(array_map('trim', $especialidadesArray));
                        }
                    }
                    if (!empty($especialidadesValidas)):
                    ?>
                    <div class="especialidades">
                        <div class="especialidades-titulo">Especialidades</div>
                        <div class="especialidades-tags">
                            <?php foreach (array_slice($especialidadesValidas, 0, 3) as $especialidad): ?>
                                <span class="especialidad-tag"><?= htmlspecialchars($especialidad) ?></span>
                            <?php endforeach; ?>
                            <?php if (count($especialidadesValidas) > 3): ?>
                                <span class="especialidad-tag">+<?= count($especialidadesValidas) - 3 ?> más</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="estadisticas-agente">
                        <div class="estadistica-item">
                            <div class="estadistica-numero"><?= number_format($agente['propiedades_activas'] ?? 0) ?></div>
                            <div class="estadistica-label">ACTIVAS</div>
                        </div>
                        <div class="estadistica-item">
                            <div class="estadistica-numero"><?= number_format($agente['propiedades_vendidas'] ?? 0) ?></div>
                            <div class="estadistica-label">VENDIDAS</div>
                        </div>
                    </div>
                    
                    <?php if (!empty($agente['calificacion_promedio'])): ?>
                    <div class="calificacion-agente">
                        <div class="calificacion-estrellas">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= $i <= $agente['calificacion_promedio'] ? '' : '-o' ?>"></i>
                            <?php endfor; ?>
                            <span class="calificacion-numero"><?= $agente['calificacion_promedio'] ?></span>
                        </div>
                        <div class="calificacion-total">(<?= $agente['total_calificaciones'] ?> reseñas)</div>
                    </div>
                    <?php endif; ?>
                    

                    
                    <?php if (!empty($agente['idiomas'])): ?>
                    <div class="idiomas-agente">
                        <div class="idiomas-titulo">Idiomas</div>
                        <div class="idiomas-tags">
                            <?php 
                            $idiomas = is_array($agente['idiomas']) ? $agente['idiomas'] : explode(',', $agente['idiomas']);
                            foreach (array_slice($idiomas, 0, 2) as $idioma): ?>
                                <span class="idioma-tag"><?= htmlspecialchars(trim($idioma)) ?></span>
                            <?php endforeach; ?>
                            <?php if (count($idiomas) > 2): ?>
                                <span class="idioma-tag">+<?= count($idiomas) - 2 ?> más</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="agente-footer">
                    <div class="acciones-agente">
                        <a href="/agente/<?= $agente['id'] ?>/perfil" class="btn-ver-perfil">
                            <i class="fas fa-eye"></i>
                            Ver Perfil Completo
                        </a>
                        
                        <a href="/chat/simple?agent=<?= $agente['id'] ?>&v=<?= time() ?>" class="btn-contactar-agente">
                            <i class="fas fa-comments"></i>
                            Contactar
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- Paginación -->
        <?php if ($totalPages > 1): ?>
        <div class="paginacion">
            <?php 
            // Construir parámetros de URL para mantener filtros
            $params = [];
            if (!empty($ciudad)) $params['ciudad'] = $ciudad;
            if (!empty($_GET['ordenar'])) $params['ordenar'] = $_GET['ordenar'];
            $queryString = http_build_query($params);
            ?>
            
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&<?= $queryString ?>" class="pagina-item">
                    <i class="fas fa-chevron-left"></i>
                    Anterior
                </a>
            <?php endif; ?>
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="?page=<?= $i ?>&<?= $queryString ?>" 
                   class="pagina-item <?= $i == $page ? 'pagina-activa' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&<?= $queryString ?>" class="pagina-item">
                    Siguiente
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="sin-resultados">
            <i class="fas fa-users"></i>
            <h3>No se encontraron agentes</h3>
            <p>
                <?php if (!empty($ciudad)): ?>
                    No hay agentes registrados en la ciudad "<?= htmlspecialchars($ciudad) ?>" con perfiles públicos activos.
                <?php else: ?>
                    No hay agentes con perfiles públicos disponibles en este momento. Los agentes pueden activar sus perfiles públicos desde su panel de control.
                <?php endif; ?>
            </p>
            <p class="mt-3">
                <a href="/" class="btn-filtrar" style="text-decoration: none; display: inline-block;">
                    <i class="fas fa-home"></i>
                    Volver al Inicio
                </a>
            </p>
        </div>
    <?php endif; ?>
</div>

<!-- Scripts específicos de la página -->
<script>
// Funcionalidad para filtros en tiempo real (opcional)
document.getElementById('ciudad').addEventListener('input', function() {
    // Aquí se podría implementar filtrado en tiempo real con AJAX
    // Por ahora, el filtrado se hace con el formulario tradicional
});

// Hacer tarjetas clickeables
document.addEventListener('DOMContentLoaded', function() {
    const agenteCards = document.querySelectorAll('.agente-card');
    agenteCards.forEach(card => {
        const link = card.querySelector('.btn-ver-perfil');
        if (link) {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function(e) {
                if (e.target.closest('.btn-ver-perfil')) return;
                window.location.href = link.href;
            });
        }
    });
});
</script> 
