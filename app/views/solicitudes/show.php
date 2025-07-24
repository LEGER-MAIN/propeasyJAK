<?php
/**
 * Vista: Detalles de Solicitud de Compra
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

$content = ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Solicitud de Compra</h1>
                <p class="mt-2 text-gray-600">Detalles de la solicitud</p>
            </div>
            <div class="flex items-center space-x-3">
                <a 
                    href="/solicitudes" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
                <a href="/chat/simple?agent=<?= $solicitud['agente_id'] ?>&v=<?= time() ?>" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white transition-all duration-200"
                   style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white;"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'; this.style.color='white'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.color='white'">
                    <i class="fas fa-comments mr-2"></i>
                    Chat con Agente
                </a>
            </div>
        </div>
    </div>

    <!-- Estado de la solicitud -->
    <div class="mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Estado de la Solicitud</h2>
                    <p class="text-sm text-gray-500">Actualizado el <?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?></p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= getEstadoBadgeClass($solicitud['estado']) ?>">
                    <?= getEstadoText($solicitud['estado']) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Información de la propiedad -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Propiedad</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($solicitud['titulo_propiedad']) ?></h3>
                <p class="text-gray-600 mt-1"><?= htmlspecialchars($solicitud['ciudad_propiedad']) ?>, <?= htmlspecialchars($solicitud['sector_propiedad']) ?></p>
                <p class="text-2xl font-bold text-primary-600 mt-2">
                    $<?= number_format($solicitud['precio_propiedad'], 2) ?> <?= $solicitud['moneda_propiedad'] ?>
                </p>
                <p class="text-sm text-gray-600 mt-2"><?= htmlspecialchars($solicitud['direccion_propiedad']) ?></p>
            </div>
            <div class="text-sm text-gray-600">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-medium">Tipo:</span> <?= ucfirst($solicitud['tipo'] ?? 'No especificado') ?>
                    </div>
                    <div>
                        <span class="font-medium">Habitaciones:</span> <?= $solicitud['habitaciones'] ?? 'No especificado' ?>
                    </div>
                    <div>
                        <span class="font-medium">Baños:</span> <?= $solicitud['banos'] ?? 'No especificado' ?>
                    </div>
                    <div>
                        <span class="font-medium">Área:</span> <?= $solicitud['metros_cuadrados'] ?? 'No especificado' ?> m²
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a 
                href="/properties/show/<?= $solicitud['propiedad_id'] ?>" 
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md font-medium transition-all duration-200 hover:transform hover:scale-105 text-center"
                style="background: linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%); color: white;"
                onmouseover="this.style.background='linear-gradient(135deg, var(--color-azul-marino-hover) 0%, var(--color-azul-marino) 100%)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(29, 53, 87, 0.3)'; this.style.color='white'"
                onmouseout="this.style.background='linear-gradient(135deg, var(--color-azul-marino) 0%, var(--color-azul-marino-hover) 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.color='white'"
            >
                <i class="fas fa-eye"></i>
                Ver detalles de la propiedad
            </a>
        </div>
    </div>

    <!-- Información del cliente y agente -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Información del cliente -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Cliente</h2>
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-semibold text-lg">
                        <?= strtoupper(substr($solicitud['nombre_cliente'], 0, 1) . substr($solicitud['apellido_cliente'], 0, 1)) ?>
                    </span>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        <?= htmlspecialchars($solicitud['nombre_cliente'] . ' ' . $solicitud['apellido_cliente']) ?>
                    </h3>
                    <p class="text-gray-600"><?= htmlspecialchars($solicitud['email_cliente']) ?></p>
                    <p class="text-gray-600"><?= htmlspecialchars($solicitud['telefono_cliente']) ?></p>
                </div>
            </div>
        </div>

        <!-- Información del agente -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Agente</h2>
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <span class="text-primary-600 font-semibold text-lg">
                        <?= strtoupper(substr($solicitud['nombre_agente'], 0, 1) . substr($solicitud['apellido_agente'], 0, 1)) ?>
                    </span>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        <?= htmlspecialchars($solicitud['nombre_agente'] . ' ' . $solicitud['apellido_agente']) ?>
                    </h3>
                    <p class="text-gray-600"><?= htmlspecialchars($solicitud['email_agente']) ?></p>
                    <p class="text-gray-600"><?= htmlspecialchars($solicitud['telefono_agente']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de la solicitud -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Detalles de la Solicitud</h2>
        <div>
            <h3 class="text-sm font-medium text-gray-700 mb-2">Mensaje del cliente</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <?php if ($solicitud['mensaje']): ?>
                    <p class="text-gray-900"><?= nl2br(htmlspecialchars($solicitud['mensaje'])) ?></p>
                <?php else: ?>
                    <p class="text-gray-500 italic">No se proporcionó mensaje</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Respuesta del agente (si existe) -->
    <?php if ($solicitud['respuesta_agente']): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Respuesta del Agente</h2>
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-gray-900"><?= nl2br(htmlspecialchars($solicitud['respuesta_agente'])) ?></p>
            <?php if ($solicitud['fecha_respuesta']): ?>
            <p class="text-sm text-gray-500 mt-2">
                Respondido el <?= date('d/m/Y H:i', strtotime($solicitud['fecha_respuesta'])) ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Acciones del agente -->
    <?php if (hasRole(ROLE_AGENTE) && $solicitud['agente_id'] == $_SESSION['user_id']): ?>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Actualizar Estado</h2>
        <form action="/solicitudes/<?= $solicitud['id'] ?>/update-status" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Nuevo Estado</label>
                <select 
                    id="estado" 
                    name="estado" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                    required
                >
                    <option value="">Seleccionar estado</option>
                    <option value="<?= REQUEST_STATUS_NEW ?>" <?= $solicitud['estado'] == REQUEST_STATUS_NEW ? 'selected' : '' ?>>Nuevo</option>
                    <option value="<?= REQUEST_STATUS_REVIEW ?>" <?= $solicitud['estado'] == REQUEST_STATUS_REVIEW ? 'selected' : '' ?>>En Revisión</option>
                    <option value="<?= REQUEST_STATUS_MEETING ?>" <?= $solicitud['estado'] == REQUEST_STATUS_MEETING ? 'selected' : '' ?>>Reunión Agendada</option>
                    <option value="<?= REQUEST_STATUS_CLOSED ?>" <?= $solicitud['estado'] == REQUEST_STATUS_CLOSED ? 'selected' : '' ?>>Cerrado</option>
                </select>
            </div>
            
            <div>
                <label for="respuesta_agente" class="block text-sm font-medium text-gray-700 mb-2">Respuesta al cliente</label>
                <textarea 
                    id="respuesta_agente" 
                    name="respuesta_agente" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Escribe tu respuesta al cliente..."
                ><?= htmlspecialchars($solicitud['respuesta_agente'] ?? '') ?></textarea>
            </div>
            
            <div class="flex justify-end space-x-4">
                <button 
                    type="submit" 
                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    Actualizar Estado
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();

// Funciones auxiliares
function getEstadoBadgeClass($estado) {
    switch ($estado) {
        case REQUEST_STATUS_NEW:
            return 'bg-yellow-100 text-yellow-800';
        case REQUEST_STATUS_REVIEW:
            return 'bg-blue-100 text-blue-800';
        case REQUEST_STATUS_MEETING:
            return 'bg-green-100 text-green-800';
        case REQUEST_STATUS_CLOSED:
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function getEstadoText($estado) {
    switch ($estado) {
        case REQUEST_STATUS_NEW:
            return 'Nuevo';
        case REQUEST_STATUS_REVIEW:
            return 'En Revisión';
        case REQUEST_STATUS_MEETING:
            return 'Reunión Agendada';
        case REQUEST_STATUS_CLOSED:
            return 'Cerrado';
        default:
            return 'Desconocido';
    }
}

include APP_PATH . '/views/layouts/main.php';
?> 