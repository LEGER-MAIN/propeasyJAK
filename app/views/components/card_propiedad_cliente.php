<?php
// Componente: Tarjeta de Propiedad del Cliente
$propiedad = $propiedad ?? [];
?>
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-200 hover:transform hover:scale-105" data-solicitud-id="<?= $propiedad['solicitud_id'] ?>">
    <!-- Imagen de la propiedad -->
    <div class="relative h-48 bg-gray-200">
        <?php if (!empty($propiedad['foto_propiedad'])): ?>
            <img src="<?= htmlspecialchars($propiedad['foto_propiedad']) ?>" 
                 alt="<?= htmlspecialchars($propiedad['titulo_propiedad']) ?>" 
                 class="w-full h-full object-cover"
                 onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\'><i class=\'fas fa-home text-4xl text-gray-400\'></i></div>'">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-home text-4xl text-gray-400"></i>
            </div>
        <?php endif; ?>
        
        <!-- Estado de la solicitud -->
        <div class="absolute top-2 right-2">
            <?php
            $estadoClass = '';
            $estadoText = '';
            switch ($propiedad['estado']) {
                case 'nuevo':
                    $estadoClass = 'bg-blue-100 text-blue-800';
                    $estadoText = 'Nueva';
                    break;
                case 'en_revision':
                    $estadoClass = 'bg-yellow-100 text-yellow-800';
                    $estadoText = 'En Revisión';
                    break;
                case 'reunion_agendada':
                    $estadoClass = 'bg-green-100 text-green-800';
                    $estadoText = 'Reunión Agendada';
                    break;
                case 'cerrado':
                    $estadoClass = 'bg-gray-100 text-gray-800';
                    $estadoText = 'Cerrado';
                    break;
                default:
                    $estadoClass = 'bg-gray-100 text-gray-800';
                    $estadoText = ucfirst($propiedad['estado']);
            }
            ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $estadoClass ?>">
                <?= $estadoText ?>
            </span>
        </div>
    </div>
    
    <!-- Información de la propiedad -->
    <div class="p-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            <?= htmlspecialchars($propiedad['titulo_propiedad']) ?>
        </h3>
        
        <!-- Ubicación -->
        <?php if (!empty($propiedad['ciudad_propiedad'])): ?>
        <div class="flex items-center text-sm text-gray-600 mb-2">
            <i class="fas fa-map-marker-alt mr-1"></i>
            <?= htmlspecialchars($propiedad['ciudad_propiedad']) ?>
            <?= !empty($propiedad['sector_propiedad']) ? ', ' . htmlspecialchars($propiedad['sector_propiedad']) : '' ?>
        </div>
        <?php endif; ?>
        
        <!-- Precio y tipo -->
        <?php if (!empty($propiedad['precio_propiedad'])): ?>
        <div class="flex items-center justify-between mb-3">
            <span class="text-lg font-bold text-primary-600">
                $<?= number_format($propiedad['precio_propiedad'], 0, ',', '.') ?>
            </span>
            <?php if (!empty($propiedad['tipo_propiedad'])): ?>
            <span class="text-sm text-gray-500">
                <?= ucfirst($propiedad['tipo_propiedad']) ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Características -->
        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
            <?php if (!empty($propiedad['habitaciones_propiedad'])): ?>
                <span><i class="fas fa-bed mr-1"></i><?= $propiedad['habitaciones_propiedad'] ?> hab</span>
            <?php endif; ?>
            <?php if (!empty($propiedad['banos_propiedad'])): ?>
                <span><i class="fas fa-bath mr-1"></i><?= $propiedad['banos_propiedad'] ?> baños</span>
            <?php endif; ?>
            <?php if (!empty($propiedad['area_propiedad'])): ?>
                <span><i class="fas fa-ruler-combined mr-1"></i><?= $propiedad['area_propiedad'] ?> m²</span>
            <?php endif; ?>
        </div>
        
        <!-- Información del agente -->
        <div class="border-t border-gray-200 pt-3">
            <div class="flex items-center">
                <?php if (!empty($propiedad['foto_agente'])): ?>
                    <img src="<?= htmlspecialchars($propiedad['foto_agente']) ?>" 
                         alt="Agente" 
                         class="w-8 h-8 rounded-full object-cover mr-2"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <?php endif; ?>
                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2" <?= !empty($propiedad['foto_agente']) ? 'style="display:none;"' : '' ?>>
                    <i class="fas fa-user text-xs text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">
                        <?= htmlspecialchars($propiedad['nombre_agente'] . ' ' . ($propiedad['apellido_agente'] ?? '')) ?>
                    </p>
                    <p class="text-xs text-gray-500">Agente</p>
                </div>
            </div>
        </div>
        
        <!-- Fecha de solicitud -->
        <div class="mt-3 text-xs text-gray-500">
            <i class="fas fa-calendar-alt mr-1"></i>
            Solicitado: <?= date('d/m/Y', strtotime($propiedad['fecha_solicitud'])) ?>
        </div>
        
        <!-- Acciones -->
        <div class="mt-4 flex space-x-2">
            <a href="/properties/show/<?= $propiedad['p_propiedad_id'] ?>" 
               class="flex-1 text-center px-3 py-2 text-sm font-medium text-primary-600 border border-primary-600 rounded-md hover:bg-primary-50 transition-colors">
                <i class="fas fa-eye mr-1"></i>Ver Propiedad
            </a>
            <a href="/chat/simple?agent=<?= $propiedad['agente_id'] ?>&v=<?= time() ?>" 
               class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white transition-all duration-200"
               style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white;"
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'; this.style.color='white'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.color='white'">
                <i class="fas fa-comments mr-2"></i>
                Chat
            </a>
        </div>
        
        <!-- Botón de eliminar (solo para estados permitidos) -->
        <?php if (in_array($propiedad['estado'], ['nuevo', 'en_revision'])): ?>
        <div class="mt-2">
            <button onclick="eliminarSolicitud(<?= $propiedad['solicitud_id'] ?>)" 
                    class="w-full text-center px-3 py-2 text-sm font-medium text-red-600 border border-red-600 rounded-md hover:bg-red-50 transition-colors">
                <i class="fas fa-trash mr-1"></i>Eliminar Solicitud
            </button>
        </div>
        <?php elseif ($propiedad['estado'] === 'cerrado'): ?>
        <div class="mt-2">
            <button onclick="eliminarSolicitud(<?= $propiedad['solicitud_id'] ?>)" 
                    class="w-full text-center px-3 py-2 text-sm font-medium text-red-600 border border-red-600 rounded-md hover:bg-red-50 transition-colors">
                <i class="fas fa-trash mr-1"></i>Eliminar Solicitud (Cerrada)
            </button>
        </div>
        <?php endif; ?>
    </div>
</div> 