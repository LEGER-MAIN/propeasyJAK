<?php
/**
 * Vista: Dashboard del Cliente
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista muestra el dashboard personalizado para clientes
 */
?>

<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard del Cliente</h1>
                    <p class="mt-2 text-gray-600">Bienvenido, <?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario') ?></p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/properties" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Buscar Propiedades
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php include APP_PATH . '/views/components/flash-messages.php'; ?>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                                <i class="fas fa-heart text-red-600"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mis Favoritos</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['favoritos'] ?? 0 ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="/favorites" class="font-medium text-primary-600 hover:text-primary-500">
                            Ver todos
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                <i class="fas fa-handshake text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mis Solicitudes</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['solicitudes'] ?? 0 ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="/solicitudes" class="font-medium text-primary-600 hover:text-primary-500">
                            Ver todas
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mis Citas</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['citas'] ?? 0 ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="/appointments" class="font-medium text-primary-600 hover:text-primary-500">
                            Ver todas
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                <i class="fas fa-home text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Propiedades Disponibles</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $totalPropiedades ?? 0 ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="/properties" class="font-medium text-primary-600 hover:text-primary-500">
                            Explorar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Favoritos Recientes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Favoritos Recientes</h2>
                    <p class="mt-1 text-sm text-gray-600">Tus propiedades favoritas</p>
                </div>
                <div class="p-6">
                    <?php if (!empty($recentFavorites)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentFavorites as $favorito): ?>
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0">
                                        <?php
                                        $img = $favorito['imagen_principal'] ?? 'default.jpg';
                                        if (strpos($img, '/') === false) {
                                            // Solo el nombre, agregamos la ruta
                                            $img = UPLOADS_URL . '/properties/' . $img;
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($img) ?>"
                                             alt="<?= htmlspecialchars($favorito['titulo'] ?? 'Propiedad') ?>"
                                             class="w-16 h-16 object-cover rounded-md"
                                             onerror="this.src='<?= UPLOADS_URL ?>/properties/default.jpg'">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 truncate">
                                            <?= htmlspecialchars($favorito['titulo'] ?? 'Propiedad') ?>
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            <?= htmlspecialchars(($favorito['ciudad'] ?? '') . (!empty($favorito['sector']) ? ', ' . $favorito['sector'] : '')) ?>
                                        </p>
                                        <p class="text-sm font-medium text-primary-600">
                                            $<?= number_format($favorito['precio'] ?? 0, 0, ',', '.') ?> <?= $favorito['moneda'] ?? 'USD' ?>
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="/properties/show/<?= $favorito['propiedad_id'] ?>" 
                                           class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                                            Ver
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 text-center">
                            <a href="/favorites" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-600 bg-primary-50 hover:bg-primary-100 transition-colors">
                                Ver todos los favoritos
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-heart text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes favoritos</h3>
                            <p class="text-gray-500 mb-4">Comienza explorando propiedades y agrégalas a tus favoritos</p>
                            <a href="/properties" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                                <i class="fas fa-search mr-2"></i>
                                Explorar Propiedades
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Solicitudes Recientes -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Solicitudes Recientes</h2>
                    <p class="mt-1 text-sm text-gray-600">Tus solicitudes de compra</p>
                </div>
                <div class="p-6">
                    <?php if (!empty($recentSolicitudes)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentSolicitudes as $solicitud): ?>
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex-shrink-0">
                                        <div class="w-16 h-16 bg-blue-100 rounded-md flex items-center justify-center">
                                            <i class="fas fa-handshake text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 truncate">
                                            <?= htmlspecialchars($solicitud['titulo_propiedad'] ?? 'Propiedad') ?>
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            Agente: <?= htmlspecialchars(($solicitud['nombre_agente'] ?? '') . ' ' . ($solicitud['apellido_agente'] ?? '')) ?>
                                        </p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?= $solicitud['estado'] === 'nuevo' ? 'bg-blue-100 text-blue-800' : 
                                                   ($solicitud['estado'] === 'en_revision' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($solicitud['estado'] === 'reunion_agendada' ? 'bg-green-100 text-green-800' : 
                                                   'bg-gray-100 text-gray-800')) ?>">
                                                <?= ucfirst(str_replace('_', ' ', $solicitud['estado'])) ?>
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                <?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="/solicitudes/<?= $solicitud['id'] ?>" 
                                           class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                                            Ver
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 text-center">
                            <a href="/solicitudes" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-600 bg-primary-50 hover:bg-primary-100 transition-colors">
                                Ver todas las solicitudes
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-handshake text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes solicitudes</h3>
                            <p class="text-gray-500 mb-4">Cuando encuentres una propiedad que te interese, puedes enviar una solicitud de compra</p>
                            <a href="/properties" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                                <i class="fas fa-search mr-2"></i>
                                Explorar Propiedades
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Citas Recientes -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Citas Recientes</h2>
                <p class="mt-1 text-sm text-gray-600">Tus citas programadas con agentes</p>
            </div>
            <div class="p-6">
                <?php if (!empty($recentCitas)): ?>
                    <div class="space-y-4">
                        <?php foreach ($recentCitas as $cita): ?>
                            <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-purple-100 rounded-md flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-purple-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">
                                        <?= htmlspecialchars($cita['propiedad_titulo'] ?? 'Propiedad') ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Agente: <?= htmlspecialchars(($cita['agente_nombre'] ?? '') . ' ' . ($cita['agente_apellido'] ?? '')) ?>
                                    </p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?= $cita['estado'] === 'propuesta' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($cita['estado'] === 'aceptada' ? 'bg-green-100 text-green-800' : 
                                               ($cita['estado'] === 'cambio_solicitado' ? 'bg-orange-100 text-orange-800' : 
                                               ($cita['estado'] === 'completada' ? 'bg-blue-100 text-blue-800' : 
                                               ($cita['estado'] === 'cancelada' ? 'bg-red-100 text-red-800' : 
                                               'bg-gray-100 text-gray-800')))) ?>">
                                            <?= ucfirst($cita['estado']) ?>
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <?= date('d/m/Y H:i', strtotime($cita['fecha_cita'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="/appointments/<?= $cita['id'] ?>" 
                                       class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                                        Ver
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="/appointments" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-600 bg-primary-50 hover:bg-primary-100 transition-colors">
                            Ver todas las citas
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-alt text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes citas</h3>
                        <p class="text-gray-500 mb-4">Cuando envíes una solicitud de compra, el agente podrá programar una cita contigo</p>
                        <a href="/properties" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Explorar Propiedades
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Acciones Rápidas</h2>
                <p class="mt-1 text-sm text-gray-600">Accede rápidamente a las funciones más importantes</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="/properties" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg border border-gray-200 hover:border-primary-300 transition-colors">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-primary-50 text-primary-600 ring-4 ring-white">
                                <i class="fas fa-search text-xl"></i>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-medium">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                Buscar Propiedades
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">Explora todas las propiedades disponibles</p>
                        </div>
                        <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>

                    <a href="/agentes" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg border border-gray-200 hover:border-primary-300 transition-colors">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-600 ring-4 ring-white">
                                <i class="fas fa-users text-xl"></i>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-medium">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                Ver Agentes
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">Encuentra agentes inmobiliarios</p>
                        </div>
                        <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>

                    <a href="/favorites" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg border border-gray-200 hover:border-primary-300 transition-colors">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-red-50 text-red-600 ring-4 ring-white">
                                <i class="fas fa-heart text-xl"></i>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-medium">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                Mis Favoritos
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">Gestiona tus propiedades favoritas</p>
                        </div>
                        <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>

                    <a href="/profile" class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-primary-500 rounded-lg border border-gray-200 hover:border-primary-300 transition-colors">
                        <div>
                            <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-600 ring-4 ring-white">
                                <i class="fas fa-user text-xl"></i>
                            </span>
                        </div>
                        <div class="mt-4">
                            <h3 class="text-lg font-medium">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                Mi Perfil
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">Actualiza tu información personal</p>
                        </div>
                        <span class="pointer-events-none absolute top-6 right-6 text-gray-300 group-hover:text-gray-400" aria-hidden="true">
                            <i class="fas fa-arrow-right"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 