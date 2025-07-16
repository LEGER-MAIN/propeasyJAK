<?php
/**
 * Vista del Perfil Público del Agente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Asegurar que las variables estén definidas
$agente = $agente ?? [];
$estadisticas = $estadisticas ?? [];
$propiedadesRecientes = $propiedadesRecientes ?? [];
$calificaciones = $calificaciones ?? [];
?>

<style>
.perfil-agente {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    margin-top: 0;
}

.perfil-header {
    text-align: center;
    margin-bottom: 2rem;
}

.foto-perfil {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    margin-bottom: 1rem;
}

.foto-perfil-default {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: #f8f9fa;
    border: 4px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.nombre-agente {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.ubicacion-agente {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.estadisticas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.estadistica-card {
    background: rgba(255,255,255,0.1);
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    backdrop-filter: blur(10px);
}

.estadistica-numero {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.estadistica-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.seccion {
    margin-bottom: 3rem;
}

.seccion-titulo {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 0.5rem;
}

.biografia {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    line-height: 1.6;
}

.especialidades {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
}

.especialidad-tag {
    background: #667eea;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.info-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.info-icon {
    width: 40px;
    height: 40px;
    background: #667eea;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.2rem;
}

.propiedades-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.propiedad-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.propiedad-card:hover {
    transform: translateY(-5px);
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
    color: #6c757d;
    font-size: 3rem;
}

.propiedad-info {
    padding: 1.5rem;
}

.propiedad-titulo {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.propiedad-precio {
    font-size: 1.3rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 0.5rem;
}

.propiedad-detalles {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.calificaciones {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.calificacion-item {
    border-bottom: 1px solid #eee;
    padding: 1rem 0;
}

.calificacion-item:last-child {
    border-bottom: none;
}

.calificacion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.calificacion-cliente {
    font-weight: 600;
    color: #333;
}

.calificacion-estrellas {
    color: #ffc107;
    font-size: 1.1rem;
}

.calificacion-fecha {
    font-size: 0.9rem;
    color: #666;
}

.calificacion-comentario {
    color: #555;
    line-height: 1.5;
}

.redes-sociales {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.red-social {
    width: 40px;
    height: 40px;
    background: #667eea;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: background 0.3s ease;
}

.red-social:hover {
    background: #5a6fd8;
    color: white;
}

/* Eliminar márgenes extra */
.max-w-7xl {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

@media (max-width: 768px) {
    .perfil-agente {
        padding: 2rem 0;
    }
    
    .nombre-agente {
        font-size: 2rem;
    }
    
    .estadisticas-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .propiedades-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Contenido del perfil público -->
<div class="perfil-agente">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="perfil-header">
            <?php if (!empty($agente['foto_perfil'])): ?>
                <img src="<?= htmlspecialchars($agente['foto_perfil']) ?>" alt="Foto de perfil" class="foto-perfil">
            <?php else: ?>
                <div class="foto-perfil-default">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>
            
            <h1 class="nombre-agente"><?= htmlspecialchars(($agente['nombre'] ?? '') . ' ' . ($agente['apellido'] ?? '')) ?></h1>
            
            <?php if (!empty($agente['ciudad']) || !empty($agente['sector'])): ?>
                <p class="ubicacion-agente">
                    <i class="fas fa-map-marker-alt"></i>
                    <?= htmlspecialchars(trim(($agente['ciudad'] ?? '') . ', ' . ($agente['sector'] ?? ''), ', ')) ?>
                </p>
            <?php endif; ?>
            
            <?php if (!empty($agente['experiencia_anos'])): ?>
                <p class="ubicacion-agente">
                    <i class="fas fa-clock"></i>
                    <?= $agente['experiencia_anos'] ?> años de experiencia
                </p>
            <?php endif; ?>
        </div>
        
        <div class="estadisticas-grid">
            <div class="estadistica-card">
                <div class="estadistica-numero"><?= number_format($estadisticas['total_propiedades'] ?? 0) ?></div>
                <div class="estadistica-label">Propiedades</div>
            </div>
            <div class="estadistica-card">
                <div class="estadistica-numero"><?= number_format($estadisticas['propiedades_vendidas'] ?? 0) ?></div>
                <div class="estadistica-label">Ventas Realizadas</div>
            </div>
            <div class="estadistica-card">
                <div class="estadistica-numero"><?= number_format($estadisticas['total_solicitudes'] ?? 0) ?></div>
                <div class="estadistica-label">Clientes Atendidos</div>
            </div>
            <div class="estadistica-card">
                <div class="estadistica-numero">
                    <?= ($estadisticas['calificacion_promedio'] ?? 0) > 0 ? number_format($estadisticas['calificacion_promedio'], 1) : 'N/A' ?>
                </div>
                <div class="estadistica-label">Calificación Promedio</div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <?php if (!empty($agente['biografia'])): ?>
    <div class="seccion">
        <h2 class="seccion-titulo">Biografía</h2>
        <div class="biografia">
            <?= nl2br(htmlspecialchars($agente['biografia'])) ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="seccion">
        <h2 class="seccion-titulo">Información Profesional</h2>
        <div class="info-grid">
            <?php 
            $especialidades = $agente['especialidades'] ?? [];
            if (!empty($especialidades)):
            ?>
            <div class="info-card">
                <h3><i class="fas fa-star info-icon"></i> Especialidades</h3>
                <div class="especialidades">
                    <?php 
                    if (is_array($especialidades)) {
                        foreach ($especialidades as $especialidad): ?>
                            <span class="especialidad-tag"><?= htmlspecialchars($especialidad) ?></span>
                        <?php endforeach;
                    } else {
                        $especialidadesArray = explode(',', $especialidades);
                        foreach ($especialidadesArray as $especialidad): ?>
                            <span class="especialidad-tag"><?= htmlspecialchars(trim($especialidad)) ?></span>
                        <?php endforeach;
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($agente['licencia_inmobiliaria'])): ?>
            <div class="info-card">
                <h3><i class="fas fa-certificate info-icon"></i> Licencia Inmobiliaria</h3>
                <p><?= htmlspecialchars($agente['licencia_inmobiliaria']) ?></p>
            </div>
            <?php endif; ?>
            
            <?php 
            $idiomas = $agente['idiomas'] ?? [];
            if (!empty($idiomas)):
            ?>
            <div class="info-card">
                <h3><i class="fas fa-language info-icon"></i> Idiomas</h3>
                <div class="especialidades">
                    <?php 
                    if (is_array($idiomas)) {
                        foreach ($idiomas as $idioma): ?>
                            <span class="especialidad-tag"><?= htmlspecialchars($idioma) ?></span>
                        <?php endforeach;
                    } else {
                        $idiomasArray = explode(',', $idiomas);
                        foreach ($idiomasArray as $idioma): ?>
                            <span class="especialidad-tag"><?= htmlspecialchars(trim($idioma)) ?></span>
                        <?php endforeach;
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($agente['horario_disponibilidad'])): ?>
            <div class="info-card">
                <h3><i class="fas fa-clock info-icon"></i> Horario de Disponibilidad</h3>
                <p><?= nl2br(htmlspecialchars($agente['horario_disponibilidad'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($propiedadesRecientes)): ?>
    <div class="seccion">
        <h2 class="seccion-titulo">Propiedades Recientes</h2>
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
                    <div class="propiedad-precio">$<?= number_format($propiedad['precio']) ?></div>
                    <div class="propiedad-detalles">
                        <span><i class="fas fa-bed"></i> <?= $propiedad['habitaciones'] ?> hab.</span>
                        <span><i class="fas fa-bath"></i> <?= $propiedad['banos'] ?> baños</span>
                        <span><i class="fas fa-ruler-combined"></i> <?= $propiedad['metros_cuadrados'] ?> m²</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($calificaciones)): ?>
    <div class="seccion">
        <h2 class="seccion-titulo">Calificaciones de Clientes</h2>
        <div class="calificaciones">
            <?php foreach ($calificaciones as $calificacion): ?>
            <div class="calificacion-item">
                <div class="calificacion-header">
                    <span class="calificacion-cliente">
                        <?= htmlspecialchars($calificacion['cliente_nombre'] . ' ' . $calificacion['cliente_apellido']) ?>
                    </span>
                    <div class="calificacion-estrellas">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $calificacion['calificacion'] ? '' : 'text-gray-300' ?>"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="calificacion-fecha">
                    <?= date('d/m/Y', strtotime($calificacion['fecha_calificacion'])) ?>
                </div>
                <?php if (!empty($calificacion['comentario'])): ?>
                <div class="calificacion-comentario">
                    <?= htmlspecialchars($calificacion['comentario']) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php 
    $redesSociales = $agente['redes_sociales'] ?? [];
    if (!empty($redesSociales) && is_string($redesSociales)):
        $redes = json_decode($redesSociales, true);
        if (!empty($redes)):
    ?>
    <div class="seccion">
        <h2 class="seccion-titulo">Redes Sociales</h2>
        <div class="redes-sociales">
            <?php foreach ($redes as $red => $url): ?>
                <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="red-social" title="<?= ucfirst($red) ?>">
                    <i class="fab fa-<?= $red ?>"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php 
        endif;
    endif; 
    ?>
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