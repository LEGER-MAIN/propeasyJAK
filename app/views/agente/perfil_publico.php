<?php
/**
 * Vista del Perfil Público del Agente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista permite a los agentes gestionar su perfil público
 */

// Asegurar que las variables estén definidas
$perfilPublico = $perfilPublico ?? [];
$estadisticas = $estadisticas ?? [];
$propiedadesRecientes = $propiedadesRecientes ?? [];
$calificaciones = $calificaciones ?? [];
?>

<style>
.perfil-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0;
    margin-bottom: 3rem;
    margin-top: 0;
}

.perfil-header-content {
    display: flex;
    align-items: center;
    gap: 3rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.foto-perfil-grande {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    border: 6px solid white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.foto-perfil-default-grande {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    border: 6px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.info-perfil {
    flex: 1;
}

.nombre-perfil {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.ubicacion-perfil {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.badges-perfil {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.badge-perfil {
    background: rgba(255,255,255,0.2);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.main-content-perfil {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.sidebar-perfil {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    height: fit-content;
}

.estadisticas-perfil {
    margin-bottom: 2rem;
}

.estadistica-perfil {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.estadistica-perfil:last-child {
    border-bottom: none;
}

.estadistica-label {
    font-weight: 600;
    color: #333;
}

.estadistica-valor {
    font-size: 1.2rem;
    font-weight: 700;
    color: #667eea;
}

.contacto-perfil {
    margin-bottom: 2rem;
}

.contacto-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    color: #666;
}

.contacto-item i {
    color: #667eea;
    width: 20px;
}

.especialidades-perfil {
    margin-bottom: 2rem;
}

.especialidades-titulo {
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.especialidades-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.especialidad-tag {
    background: #f8f9fa;
    color: #667eea;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.idiomas-perfil {
    margin-bottom: 2rem;
}

.idiomas-titulo {
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.idiomas-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.idioma-tag {
    background: #e8f4fd;
    color: #2196f3;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.contenido-principal {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.seccion-perfil {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.seccion-titulo {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.descripcion-perfil {
    line-height: 1.6;
    color: #666;
    font-size: 1.1rem;
}

.propiedades-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.propiedad-card {
    border: 1px solid #eee;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.propiedad-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.propiedad-imagen {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.propiedad-imagen-default {
    width: 100%;
    height: 200px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 3rem;
}

.propiedad-info {
    padding: 1.5rem;
}

.propiedad-titulo {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
}

.propiedad-precio {
    font-size: 1.2rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 0.5rem;
}

.propiedad-ubicacion {
    color: #666;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.calificaciones-lista {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.calificacion-item {
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 1.5rem;
}

.calificacion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.calificacion-cliente {
    font-weight: 600;
    color: #333;
}

.calificacion-estrellas {
    color: #ffc107;
    font-size: 1.1rem;
}

.calificacion-comentario {
    color: #666;
    line-height: 1.5;
    font-style: italic;
}

.calificacion-fecha {
    color: #999;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.btn-contactar {
    background: #667eea;
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1.1rem;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: background 0.3s ease;
    margin-top: 1rem;
}

.btn-contactar:hover {
    background: #5a6fd8;
    color: white;
    text-decoration: none;
}

.sin-propiedades, .sin-calificaciones {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.sin-propiedades i, .sin-calificaciones i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .perfil-header-content {
        flex-direction: column;
        text-align: center;
        gap: 2rem;
    }
    
    .main-content-perfil {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .propiedades-grid {
        grid-template-columns: 1fr;
    }
    
    .badges-perfil {
        justify-content: center;
    }
}
</style>

<!-- Header del perfil -->
<div class="perfil-header">
    <div class="perfil-header-content">
        <?php if (!empty($perfilPublico['foto_perfil'])): ?>
            <img src="<?= htmlspecialchars($perfilPublico['foto_perfil']) ?>" alt="Foto de perfil" class="foto-perfil-grande">
        <?php else: ?>
            <div class="foto-perfil-default-grande">
                <i class="fas fa-user"></i>
            </div>
        <?php endif; ?>
        
        <div class="info-perfil">
            <h1 class="nombre-perfil"><?= htmlspecialchars(($perfilPublico['nombre'] ?? '') . ' ' . ($perfilPublico['apellido'] ?? '')) ?></h1>
            
            <?php if (!empty($perfilPublico['ciudad'])): ?>
                <div class="ubicacion-perfil">
                    <i class="fas fa-map-marker-alt"></i>
                    <?= htmlspecialchars($perfilPublico['ciudad']) ?>
                    <?= !empty($perfilPublico['sector']) ? ', ' . htmlspecialchars($perfilPublico['sector']) : '' ?>
                </div>
            <?php endif; ?>
            
            <div class="badges-perfil">
                <?php if (!empty($perfilPublico['experiencia_anos'])): ?>
                    <span class="badge-perfil">
                        <i class="fas fa-clock"></i>
                        <?= $perfilPublico['experiencia_anos'] ?> años de experiencia
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($perfilPublico['licencia_inmobiliaria'])): ?>
                    <span class="badge-perfil">
                        <i class="fas fa-certificate"></i>
                        Lic. <?= htmlspecialchars($perfilPublico['licencia_inmobiliaria']) ?>
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($estadisticas['calificacion_promedio'])): ?>
                    <span class="badge-perfil">
                        <i class="fas fa-star"></i>
                        <?= number_format($estadisticas['calificacion_promedio'], 1) ?> / 5.0
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Contenido principal -->
<div class="main-content-perfil">
    <!-- Sidebar con información de contacto y estadísticas -->
    <div class="sidebar-perfil">
        <!-- Estadísticas -->
        <div class="estadisticas-perfil">
            <h3>Estadísticas</h3>
            <div class="estadistica-perfil">
                <span class="estadistica-label">Propiedades Activas</span>
                <span class="estadistica-valor"><?= number_format($estadisticas['propiedades_activas'] ?? 0) ?></span>
            </div>
            <div class="estadistica-perfil">
                <span class="estadistica-label">Propiedades Vendidas</span>
                <span class="estadistica-valor"><?= number_format($estadisticas['propiedades_vendidas'] ?? 0) ?></span>
            </div>
            <div class="estadistica-perfil">
                <span class="estadistica-label">Solicitudes Recibidas</span>
                <span class="estadistica-valor"><?= number_format($estadisticas['total_solicitudes'] ?? 0) ?></span>
            </div>
            <div class="estadistica-perfil">
                <span class="estadistica-label">Citas Realizadas</span>
                <span class="estadistica-valor"><?= number_format($estadisticas['total_citas'] ?? 0) ?></span>
            </div>
        </div>
        
        <!-- Información de contacto -->
        <div class="contacto-perfil">
            <h3>Información de Contacto</h3>
            <?php if (!empty($perfilPublico['email'])): ?>
                <div class="contacto-item">
                    <i class="fas fa-envelope"></i>
                    <span><?= htmlspecialchars($perfilPublico['email']) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($perfilPublico['telefono'])): ?>
                <div class="contacto-item">
                    <i class="fas fa-phone"></i>
                    <span><?= htmlspecialchars($perfilPublico['telefono']) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($perfilPublico['horario_atencion'])): ?>
                <div class="contacto-item">
                    <i class="fas fa-clock"></i>
                    <span><?= htmlspecialchars($perfilPublico['horario_atencion']) ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Especialidades -->
        <?php 
        $especialidades = $perfilPublico['especialidades'] ?? [];
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
        <div class="especialidades-perfil">
            <div class="especialidades-titulo">Especialidades</div>
            <div class="especialidades-tags">
                <?php foreach ($especialidadesValidas as $especialidad): ?>
                    <span class="especialidad-tag"><?= htmlspecialchars($especialidad) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Idiomas -->
        <?php if (!empty($perfilPublico['idiomas'])): ?>
        <div class="idiomas-perfil">
            <div class="idiomas-titulo">Idiomas</div>
            <div class="idiomas-tags">
                <?php 
                $idiomas = is_array($perfilPublico['idiomas']) ? $perfilPublico['idiomas'] : explode(',', $perfilPublico['idiomas']);
                foreach ($idiomas as $idioma): ?>
                    <span class="idioma-tag"><?= htmlspecialchars(trim($idioma)) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Botón de contacto -->
        <a href="/chat/iniciar/<?= $perfilPublico['id'] ?>" class="btn-contactar">
            <i class="fas fa-comments"></i>
            Contactar Agente
        </a>
    </div>
    
    <!-- Contenido principal -->
    <div class="contenido-principal">
        <!-- Descripción -->
        <?php if (!empty($perfilPublico['descripcion_completa'])): ?>
        <div class="seccion-perfil">
            <h2 class="seccion-titulo">
                <i class="fas fa-user-tie"></i>
                Sobre Mí
            </h2>
            <div class="descripcion-perfil">
                <?= nl2br(htmlspecialchars($perfilPublico['descripcion_completa'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Propiedades Recientes -->
        <div class="seccion-perfil">
            <h2 class="seccion-titulo">
                <i class="fas fa-home"></i>
                Propiedades Recientes
            </h2>
            
            <?php if (!empty($propiedadesRecientes)): ?>
                <div class="propiedades-grid">
                    <?php foreach ($propiedadesRecientes as $propiedad): ?>
                    <div class="propiedad-card">
                        <?php if (!empty($propiedad['imagen_principal'])): ?>
                            <img src="<?= htmlspecialchars($propiedad['imagen_principal']) ?>" alt="<?= htmlspecialchars($propiedad['titulo']) ?>" class="propiedad-imagen">
                        <?php else: ?>
                            <div class="propiedad-imagen-default">
                                <i class="fas fa-home"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="propiedad-info">
                            <h3 class="propiedad-titulo"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                            <div class="propiedad-precio">
                                $<?= number_format($propiedad['precio']) ?>
                                <?= !empty($propiedad['moneda']) ? $propiedad['moneda'] : 'USD' ?>
                            </div>
                            <div class="propiedad-ubicacion">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($propiedad['ciudad']) ?>
                                <?= !empty($propiedad['sector']) ? ', ' . htmlspecialchars($propiedad['sector']) : '' ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sin-propiedades">
                    <i class="fas fa-home"></i>
                    <h3>No hay propiedades disponibles</h3>
                    <p>Este agente aún no ha publicado propiedades en el sistema.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Calificaciones -->
        <div class="seccion-perfil">
            <h2 class="seccion-titulo">
                <i class="fas fa-star"></i>
                Calificaciones y Comentarios
            </h2>
            
            <?php if (!empty($calificaciones)): ?>
                <div class="calificaciones-lista">
                    <?php foreach ($calificaciones as $calificacion): ?>
                    <div class="calificacion-item">
                        <div class="calificacion-header">
                            <span class="calificacion-cliente"><?= htmlspecialchars($calificacion['nombre_cliente'] ?? 'Cliente') ?></span>
                            <span class="calificacion-estrellas">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?= $i <= ($calificacion['calificacion'] ?? 0) ? '' : '-o' ?>"></i>
                                <?php endfor; ?>
                            </span>
                        </div>
                        <?php if (!empty($calificacion['comentario'])): ?>
                            <div class="calificacion-comentario">
                                "<?= htmlspecialchars($calificacion['comentario']) ?>"
                            </div>
                        <?php endif; ?>
                        <div class="calificacion-fecha">
                            <?= date('d/m/Y', strtotime($calificacion['fecha_calificacion'] ?? 'now')) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sin-calificaciones">
                    <i class="fas fa-star"></i>
                    <h3>No hay calificaciones aún</h3>
                    <p>Este agente aún no ha recibido calificaciones de clientes.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Agregar funcionalidad para hacer las propiedades clickeables
document.addEventListener('DOMContentLoaded', function() {
    const propiedadCards = document.querySelectorAll('.propiedad-card');
    propiedadCards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
            const propiedadId = this.dataset.propiedadId;
            if (propiedadId) {
                window.location.href = '/properties/show/' + propiedadId;
            }
        });
    });
});
</script> 