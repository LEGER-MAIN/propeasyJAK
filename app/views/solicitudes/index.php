<?php
/**
 * Vista: Listado de Solicitudes
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

$content = ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <?= hasRole(ROLE_AGENTE) ? 'Solicitudes Recibidas' : 'Mis Solicitudes' ?>
        </h1>
        <p class="mt-2 text-gray-600">
            <?= hasRole(ROLE_AGENTE) ? 'Gestiona las solicitudes de compra que has recibido' : 'Revisa el estado de tus solicitudes de compra' ?>
        </p>
    </div>

    <!-- Estadísticas -->
    <?php if ($estadisticas): ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $estadisticas['total_solicitudes'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Nuevas</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $estadisticas['solicitudes_nuevas'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Reuniones</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $estadisticas['solicitudes_reunion'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Cerradas</p>
                    <p class="text-2xl font-semibold text-gray-900"><?= $estadisticas['solicitudes_cerradas'] ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Lista de solicitudes -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <?php if (empty($solicitudes)): ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay solicitudes</h3>
            <p class="mt-1 text-sm text-gray-500">
                <?= hasRole(ROLE_AGENTE) ? 'No has recibido solicitudes de compra aún.' : 'No has enviado solicitudes de compra aún.' ?>
            </p>
            <?php if (!hasRole(ROLE_AGENTE)): ?>
            <div class="mt-6">
                <a href="/properties" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    Ver Propiedades
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <ul class="divide-y divide-gray-200">
            <?php foreach ($solicitudes as $solicitud): ?>
            <li>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($solicitud['titulo_propiedad']) ?>
                                    </p>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= getEstadoBadgeClass($solicitud['estado']) ?>">
                                        <?= getEstadoText($solicitud['estado']) ?>
                                    </span>
                                </div>
                                <div class="mt-1 flex items-center text-sm text-gray-500">
                                    <p class="mr-4">
                                        <span class="font-medium">Precio:</span> 
                                        $<?= number_format($solicitud['precio_propiedad'], 2) ?> <?= $solicitud['moneda_propiedad'] ?>
                                    </p>
                                    <p class="mr-4">
                                        <span class="font-medium">Ubicación:</span> 
                                        <?= htmlspecialchars($solicitud['ciudad_propiedad']) ?>, <?= htmlspecialchars($solicitud['sector_propiedad']) ?>
                                    </p>
                                    <p>
                                        <span class="font-medium">Fecha:</span> 
                                        <?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'])) ?>
                                    </p>
                                </div>
                                <?php if (hasRole(ROLE_AGENTE)): ?>
                                <div class="mt-1 text-sm text-gray-500">
                                    <span class="font-medium">Cliente:</span> 
                                    <?= htmlspecialchars($solicitud['nombre_cliente'] . ' ' . $solicitud['apellido_cliente']) ?>
                                    (<?= htmlspecialchars($solicitud['email_cliente']) ?>)
                                </div>
                                <?php else: ?>
                                <div class="mt-1 text-sm text-gray-500">
                                    <span class="font-medium">Agente:</span> 
                                    <?= htmlspecialchars($solicitud['nombre_agente'] . ' ' . $solicitud['apellido_agente']) ?>
                                    (<?= htmlspecialchars($solicitud['email_agente']) ?>)
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a 
                                href="/solicitudes/<?= $solicitud['id'] ?>" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            >
                                Ver Detalles
                            </a>
                            <a 
                                href="/chat/<?= $solicitud['id'] ?>" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            >
                                Chat
                            </a>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

    <!-- Paginación -->
    <?php if (!empty($solicitudes) && count($solicitudes) >= 10): ?>
    <div class="mt-6 flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Anterior
            </a>
            <?php endif; ?>
            <a href="?page=<?= $page + 1 ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Siguiente
            </a>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando página <span class="font-medium"><?= $page ?></span>
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Anterior</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <?php endif; ?>
                    <a href="?page=<?= $page + 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Siguiente</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </nav>
            </div>
        </div>
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