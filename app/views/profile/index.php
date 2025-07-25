<?php
/**
 * Vista: Perfil Unificado del Usuario
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta vista permite a todos los usuarios gestionar su perfil con funcionalidad completa
 */

$userRole = $_SESSION['user_rol'] ?? 'cliente';
$roleNames = [
    'cliente' => 'Cliente',
    'agente' => 'Agente Inmobiliario',
    'admin' => 'Administrador'
];
?>

<div class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Mi Perfil</h1>
                    <p class="mt-2 text-gray-600">Gestiona tu información personal y revisa tus actividades</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/dashboard" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes Flash -->
        <?php include APP_PATH . '/views/components/flash-messages.php'; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Información del Perfil -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Información Personal</h2>
                        <p class="mt-1 text-sm text-gray-600">Actualiza tu información personal y de contacto</p>
                    </div>
                    
                    <form action="/profile" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <!-- Foto de Perfil -->
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                <div id="profile-preview" class="relative">
                                    <?php if (!empty($user['foto_perfil'])): ?>
                                        <img id="current-photo" class="h-24 w-24 rounded-full object-cover border-4 border-gray-200 shadow-lg" 
                                             src="<?= htmlspecialchars($user['foto_perfil']) ?>" 
                                             alt="Foto de perfil">
                                    <?php else: ?>
                                        <div id="default-photo" class="h-24 w-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center border-4 border-gray-200 shadow-lg">
                                            <i class="fas fa-user text-3xl text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Overlay de carga -->
                                    <div id="upload-overlay" class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center hidden">
                                        <i class="fas fa-spinner fa-spin text-white text-xl"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label for="foto_perfil" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cambiar Foto de Perfil
                                </label>
                                <div class="relative">
                                    <input type="file" 
                                           id="foto_perfil" 
                                           name="foto_perfil" 
                                           accept="image/*"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200">
                                    <div id="file-info" class="mt-2 text-sm text-gray-500 hidden">
                                        <span id="file-name" class="font-medium text-green-600"></span>
                                        <span id="file-size" class="text-gray-400"></span>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                    Formatos: JPG, PNG, GIF. Máximo 5MB.
                                </p>
                                <div id="upload-error" class="mt-2 text-sm text-red-600 hidden">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <span id="error-message"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nombre y Apellido -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="<?= htmlspecialchars($user['nombre'] ?? '') ?>" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="apellido" class="block text-sm font-medium text-gray-700 mb-2">
                                    Apellido <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="apellido" 
                                       name="apellido" 
                                       value="<?= htmlspecialchars($user['apellido'] ?? '') ?>" 
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                                   disabled
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                            <p class="mt-1 text-sm text-gray-500">El email no se puede cambiar por seguridad</p>
                        </div>
                        
                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono
                            </label>
                            <input type="tel" 
                                   id="telefono" 
                                   name="telefono" 
                                   value="<?= htmlspecialchars($user['telefono'] ?? '') ?>" 
                                   placeholder="809-123-4567"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        
                        <!-- Ciudad y Sector -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ciudad
                                </label>
                                <input type="text" 
                                       id="ciudad" 
                                       name="ciudad" 
                                       value="<?= htmlspecialchars($user['ciudad'] ?? '') ?>" 
                                       placeholder="Santo Domingo"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <div>
                                <label for="sector" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sector
                                </label>
                                <input type="text" 
                                       id="sector" 
                                       name="sector" 
                                       value="<?= htmlspecialchars($user['sector'] ?? '') ?>" 
                                       placeholder="Invivienda"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        
                        <?php if ($userRole === 'agente'): ?>
                        <!-- Campos específicos para agentes -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Profesional</h3>
                            
                            <!-- Experiencia -->
                            <div>
                                <label for="experiencia_anos" class="block text-sm font-medium text-gray-700 mb-2">
                                    Años de Experiencia
                                </label>
                                <input type="number" 
                                       id="experiencia_anos" 
                                       name="experiencia_anos" 
                                       value="<?= htmlspecialchars($user['experiencia_anos'] ?? 0) ?>" 
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <!-- Especialidades -->
                            <div>
                                <label for="especialidades" class="block text-sm font-medium text-gray-700 mb-2">
                                    Especialidades
                                </label>
                                <input type="text" 
                                       id="especialidades" 
                                       name="especialidades" 
                                       value="<?= htmlspecialchars($user['especialidades'] ?? '') ?>" 
                                       placeholder="Venta, Casas, Apartamentos, Terrenos"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">Separar con comas</p>
                            </div>
                            
                            <!-- Idiomas -->
                            <div>
                                <label for="idiomas" class="block text-sm font-medium text-gray-700 mb-2">
                                    Idiomas
                                </label>
                                <input type="text" 
                                       id="idiomas" 
                                       name="idiomas" 
                                       value="<?= htmlspecialchars($user['idiomas'] ?? '') ?>" 
                                       placeholder="Español, Inglés, Francés"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">Separar con comas</p>
                            </div>
                            
                            <!-- Licencia Inmobiliaria -->
                            <div>
                                <label for="licencia_inmobiliaria" class="block text-sm font-medium text-gray-700 mb-2">
                                    Licencia Inmobiliaria
                                </label>
                                <input type="text" 
                                       id="licencia_inmobiliaria" 
                                       name="licencia_inmobiliaria" 
                                       value="<?= htmlspecialchars($user['licencia_inmobiliaria'] ?? '') ?>" 
                                       placeholder="LIC-12345"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            
                            <!-- Horario de Disponibilidad -->
                            <div>
                                <label for="horario_disponibilidad" class="block text-sm font-medium text-gray-700 mb-2">
                                    Horario de Disponibilidad
                                </label>
                                <textarea id="horario_disponibilidad" 
                                          name="horario_disponibilidad" 
                                          rows="3"
                                          placeholder="Lunes a Viernes: 9:00 AM - 6:00 PM&#10;Sábados: 9:00 AM - 2:00 PM"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"><?= htmlspecialchars($user['horario_disponibilidad'] ?? '') ?></textarea>
                            </div>
                            
                            <!-- Biografía -->
                            <div>
                                <label for="biografia" class="block text-sm font-medium text-gray-700 mb-2">
                                    Biografía
                                </label>
                                <textarea id="biografia" 
                                          name="biografia" 
                                          rows="4"
                                          placeholder="Cuéntanos sobre tu experiencia y especialidades..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"><?= htmlspecialchars($user['biografia'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Cambiar Contraseña -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Contraseña</h3>
                            <p class="text-sm text-gray-600 mb-4">Deja en blanco si no quieres cambiar la contraseña</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nueva Contraseña
                                    </label>
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <p class="mt-1 text-sm text-gray-500">Mínimo 8 caracteres</p>
                                </div>
                                
                                <div>
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirmar Contraseña
                                    </label>
                                    <input type="password" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botón de Guardar -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Información de la Cuenta -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Cuenta</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Estado:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <?= ucfirst($user['estado'] ?? 'activo') ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Rol:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?= $roleNames[$userRole] ?? 'Usuario' ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Email Verificado:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= ($user['email_verificado'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ($user['email_verificado'] ?? false) ? 'Sí' : 'No' ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Miembro desde:</span>
                            <span class="text-sm text-gray-900">
                                <?= date('d/m/Y', strtotime($user['fecha_registro'] ?? 'now')) ?>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Último acceso:</span>
                            <span class="text-sm text-gray-900">
                                <?= $user['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) : 'Nunca' ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Mis Estadísticas -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Mis Estadísticas</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <?php if ($userRole === 'cliente'): ?>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600"><?= $stats['favoritos'] ?? 0 ?></div>
                                <div class="text-sm text-gray-500">Favoritos</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600"><?= $stats['solicitudes'] ?? 0 ?></div>
                                <div class="text-sm text-gray-500">Solicitudes</div>
                            </div>
                        <?php elseif ($userRole === 'agente'): ?>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600"><?= $stats['propiedades_activas'] ?? 0 ?></div>
                                <div class="text-sm text-gray-500">Propiedades Activas</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600"><?= $stats['propiedades_vendidas'] ?? 0 ?></div>
                                <div class="text-sm text-gray-500">Propiedades Vendidas</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600"><?= $stats['solicitudes_pendientes'] ?? 0 ?></div>
                                <div class="text-sm text-gray-500">Solicitudes Pendientes</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">$<?= number_format($stats['total_ventas'] ?? 0, 0) ?></div>
                                <div class="text-sm text-gray-500">Total Ventas</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Acciones Rápidas -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                    <div class="space-y-3">
                        <?php if ($userRole === 'cliente'): ?>
                            <a href="/favorites" class="flex items-center text-sm text-gray-700 hover:text-primary-600 transition-colors">
                                <i class="fas fa-heart mr-3 text-gray-400"></i>
                                Mis Favoritos
                            </a>
                            <a href="/solicitudes" class="flex items-center text-sm text-gray-700 hover:text-primary-600 transition-colors">
                                <i class="fas fa-file-alt mr-3 text-gray-400"></i>
                                Mis Solicitudes
                            </a>
                            <a href="/properties" class="flex items-center text-sm text-gray-700 hover:text-primary-600 transition-colors">
                                <i class="fas fa-search mr-3 text-gray-400"></i>
                                Buscar Propiedades
                            </a>
                            <a href="/agentes" class="flex items-center text-sm text-gray-700 hover:text-primary-600 transition-colors">
                                <i class="fas fa-users mr-3 text-gray-400"></i>
                                Ver Agentes
                            </a>
                        <?php elseif ($userRole === 'agente'): ?>
                            <a href="/properties/agent/list" class="flex items-center text-sm text-gray-700 hover:text-primary-600 transition-colors">
                                <i class="fas fa-list mr-3 text-gray-400"></i>
                                Mis Propiedades
                            </a>
                            <a href="/properties/pending-validation" class="flex items-center text-sm text-gray-700 hover:text-primary-600 transition-colors">
                                <i class="fas fa-clock mr-3 text-gray-400"></i>
                                Propiedades Pendientes
                            </a>
                            <a href="/agente/perfil-publico" class="flex items-center text-sm text-gray-700 hover:text-primary-600 transition-colors">
                                <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                                Mi Perfil Público
                            </a>
                            <a href="/appointments" class="flex items-center text-sm text-gray-700 hover:text-primary-600 transition-colors">
                                <i class="fas fa-calendar-alt mr-3 text-gray-400"></i>
                                Mis Citas
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($userRole === 'cliente' && !empty($propiedadesEnviadas)): ?>
        <!-- Propiedades Enviadas como Solicitudes -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Propiedades Enviadas como Solicitudes</h2>
                    <p class="mt-1 text-sm text-gray-600">Propiedades que has solicitado información o compra</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($propiedadesEnviadas as $solicitud): ?>
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-200 hover:transform hover:scale-105">
                            <!-- Imagen de la propiedad -->
                            <div class="relative h-48 bg-gray-200">
                                <?php if (!empty($solicitud['foto_propiedad'])): ?>
                                    <img src="<?= htmlspecialchars($solicitud['foto_propiedad']) ?>" 
                                         alt="<?= htmlspecialchars($solicitud['titulo_propiedad']) ?>" 
                                         class="w-full h-full object-cover">
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
                                    switch ($solicitud['estado']) {
                                        case 'nueva':
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
                                            $estadoText = ucfirst($solicitud['estado']);
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
                                    <?= htmlspecialchars($solicitud['titulo_propiedad']) ?>
                                </h3>
                                
                                <div class="flex items-center text-sm text-gray-600 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?= htmlspecialchars($solicitud['ciudad_propiedad']) ?>
                                    <?= !empty($solicitud['sector_propiedad']) ? ', ' . htmlspecialchars($solicitud['sector_propiedad']) : '' ?>
                                </div>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-bold text-primary-600">
                                        $<?= number_format($solicitud['precio_propiedad'], 0) ?>
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        <?= ucfirst($solicitud['tipo_propiedad']) ?>
                                    </span>
                                </div>
                                
                                <!-- Características -->
                                <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                                    <?php if (!empty($solicitud['habitaciones_propiedad'])): ?>
                                        <span><i class="fas fa-bed mr-1"></i><?= $solicitud['habitaciones_propiedad'] ?> hab</span>
                                    <?php endif; ?>
                                    <?php if (!empty($solicitud['banos_propiedad'])): ?>
                                        <span><i class="fas fa-bath mr-1"></i><?= $solicitud['banos_propiedad'] ?> baños</span>
                                    <?php endif; ?>
                                    <?php if (!empty($solicitud['area_propiedad'])): ?>
                                        <span><i class="fas fa-ruler-combined mr-1"></i><?= $solicitud['area_propiedad'] ?> m²</span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Información del agente -->
                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex items-center">
                                        <?php if (!empty($solicitud['foto_agente'])): ?>
                                            <img src="<?= htmlspecialchars($solicitud['foto_agente']) ?>" 
                                                 alt="Agente" 
                                                 class="w-8 h-8 rounded-full object-cover mr-2">
                                        <?php else: ?>
                                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                                <i class="fas fa-user text-xs text-gray-600"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($solicitud['nombre_agente'] . ' ' . $solicitud['apellido_agente']) ?>
                                            </p>
                                            <p class="text-xs text-gray-500">Agente</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Fecha de solicitud -->
                                <div class="mt-3 text-xs text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Solicitado: <?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'])) ?>
                                </div>
                                
                                <!-- Acciones -->
                                <div class="mt-4 flex space-x-2">
                                    <a href="/properties/<?= $solicitud['propiedad_id'] ?>" 
                                       class="flex-1 text-center px-3 py-2 text-sm font-medium text-primary-600 border border-primary-600 rounded-md hover:bg-primary-50 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>Ver Propiedad
                                    </a>
                                    <a href="/chat/simple?agent=<?= $solicitud['agente_id'] ?>&v=<?= time() ?>" 
                                       class="flex-1 text-center px-3 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 transition-colors">
                                        <i class="fas fa-comments mr-1"></i>Chat
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('foto_perfil');
    const profilePreview = document.getElementById('profile-preview');
    const currentPhoto = document.getElementById('current-photo');
    const defaultPhoto = document.getElementById('default-photo');
    const uploadOverlay = document.getElementById('upload-overlay');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const uploadError = document.getElementById('upload-error');
    const errorMessage = document.getElementById('error-message');

    // Función para mostrar error
    function showError(message) {
        errorMessage.textContent = message;
        uploadError.classList.remove('hidden');
        setTimeout(() => {
            uploadError.classList.add('hidden');
        }, 5000);
    }

    // Función para ocultar error
    function hideError() {
        uploadError.classList.add('hidden');
    }

    // Función para mostrar información del archivo
    function showFileInfo(name, size) {
        fileName.textContent = name;
        fileSize.textContent = ` (${size})`;
        fileInfo.classList.remove('hidden');
    }

    // Función para ocultar información del archivo
    function hideFileInfo() {
        fileInfo.classList.add('hidden');
    }

    // Función para mostrar overlay de carga
    function showUploadOverlay() {
        uploadOverlay.classList.remove('hidden');
    }

    // Función para ocultar overlay de carga
    function hideUploadOverlay() {
        uploadOverlay.classList.add('hidden');
    }

    // Función para actualizar vista previa
    function updatePreview(imageUrl) {
        if (currentPhoto) {
            currentPhoto.src = imageUrl;
            currentPhoto.style.display = 'block';
        } else {
            // Crear nueva imagen si no existe
            const newImg = document.createElement('img');
            newImg.id = 'current-photo';
            newImg.className = 'h-24 w-24 rounded-full object-cover border-4 border-gray-200 shadow-lg';
            newImg.src = imageUrl;
            newImg.alt = 'Foto de perfil';
            profilePreview.appendChild(newImg);
        }
        
        // Ocultar foto por defecto si existe
        if (defaultPhoto) {
            defaultPhoto.style.display = 'none';
        }
    }

    // Manejar cambio de archivo
    if (fotoInput) {
        fotoInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (!file) {
                hideFileInfo();
                hideError();
                return;
            }

            // Validar tipo de archivo
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showError('Solo se permiten archivos JPG, PNG y GIF');
                this.value = '';
                hideFileInfo();
                return;
            }

            // Validar tamaño (5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB en bytes
            if (file.size > maxSize) {
                showError('El archivo es demasiado grande. Máximo 5MB');
                this.value = '';
                hideFileInfo();
                return;
            }

            // Mostrar información del archivo
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            showFileInfo(file.name, `${sizeInMB} MB`);
            hideError();

            // Mostrar overlay de carga
            showUploadOverlay();

            // Crear vista previa
            const reader = new FileReader();
            reader.onload = function(e) {
                updatePreview(e.target.result);
                hideUploadOverlay();
            };
            reader.onerror = function() {
                showError('Error al leer el archivo');
                hideUploadOverlay();
            };
            reader.readAsDataURL(file);
        });
    }

    // Validación de contraseña en tiempo real
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value : '';
            
            if (password.length > 0 && password.length < 8) {
                this.setCustomValidity('La contraseña debe tener al menos 8 caracteres');
            } else {
                this.setCustomValidity('');
            }
            
            if (confirmPassword && password !== confirmPassword) {
                if (confirmPasswordInput) {
                    confirmPasswordInput.setCustomValidity('Las contraseñas no coinciden');
                }
            } else {
                if (confirmPasswordInput) {
                    confirmPasswordInput.setCustomValidity('');
                }
            }
        });
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput ? passwordInput.value : '';
            const confirmPassword = this.value;
            
            if (password && confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
</script> 
