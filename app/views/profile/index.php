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
                                <?php if (!empty($user['foto_perfil'])): ?>
                                    <img class="h-24 w-24 rounded-full object-cover border-4 border-gray-200" 
                                         src="<?= htmlspecialchars($user['foto_perfil']) ?>" 
                                         alt="Foto de perfil">
                                <?php else: ?>
                                    <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center border-4 border-gray-200">
                                        <i class="fas fa-user text-3xl text-gray-500"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <label for="foto_perfil" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cambiar Foto de Perfil
                                </label>
                                <input type="file" 
                                       id="foto_perfil" 
                                       name="foto_perfil" 
                                       accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <p class="mt-1 text-sm text-gray-500">Formatos: JPG, PNG, GIF. Máximo 5MB.</p>
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
                                       placeholder="Venta, Alquiler, Casas, Apartamentos"
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
                        <a href="/settings" class="flex items-center text-sm text-gray-700 hover:text-primary-600 transition-colors">
                            <i class="fas fa-cog mr-3 text-gray-400"></i>
                            Configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación de contraseña en tiempo real
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password.length > 0 && password.length < 8) {
        this.setCustomValidity('La contraseña debe tener al menos 8 caracteres');
    } else {
        this.setCustomValidity('');
    }
    
    if (confirmPassword && password !== confirmPassword) {
        document.getElementById('confirm_password').setCustomValidity('Las contraseñas no coinciden');
    } else {
        document.getElementById('confirm_password').setCustomValidity('');
    }
});

document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password && confirmPassword && password !== confirmPassword) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});

// Vista previa de imagen
document.getElementById('foto_perfil').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('.flex-shrink-0 img');
            if (img) {
                img.src = e.target.result;
            } else {
                const div = document.querySelector('.flex-shrink-0 div');
                if (div) {
                    div.innerHTML = `<img class="h-24 w-24 rounded-full object-cover border-4 border-gray-200" src="${e.target.result}" alt="Foto de perfil">`;
                }
            }
        };
        reader.readAsDataURL(file);
    }
});
</script> 