<?php
/**
 * Vista: Crear Solicitud de Compra
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

$content = ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Solicitar Compra</h1>
        <p class="mt-2 text-gray-600">Completa el formulario para enviar tu solicitud de compra</p>
    </div>

    <!-- Información de la propiedad -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Información de la Propiedad</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($propiedad['titulo']) ?></h3>
                <p class="text-gray-600 mt-1"><?= htmlspecialchars($propiedad['ciudad']) ?>, <?= htmlspecialchars($propiedad['sector']) ?></p>
                <p class="text-2xl font-bold text-primary-600 mt-2">
                    $<?= number_format($propiedad['precio'], 2) ?> <?= $propiedad['moneda'] ?>
                </p>
            </div>
            <div class="text-sm text-gray-600">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-medium">Tipo:</span> <?= ucfirst($propiedad['tipo']) ?>
                    </div>
                    <div>
                        <span class="font-medium">Habitaciones:</span> <?= $propiedad['habitaciones'] ?>
                    </div>
                    <div>
                        <span class="font-medium">Baños:</span> <?= $propiedad['banos'] ?>
                    </div>
                    <div>
                        <span class="font-medium">Área:</span> <?= $propiedad['metros_cuadrados'] ?> m²
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del agente -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Agente Responsable</h2>
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                <span class="text-primary-600 font-semibold text-lg">
                    <?= strtoupper(substr($agente['nombre'], 0, 1) . substr($agente['apellido'], 0, 1)) ?>
                </span>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">
                    <?= htmlspecialchars($agente['nombre'] . ' ' . $agente['apellido']) ?>
                </h3>
                <p class="text-gray-600"><?= htmlspecialchars($agente['email']) ?></p>
                <p class="text-gray-600"><?= htmlspecialchars($agente['telefono']) ?></p>
            </div>
        </div>
    </div>

    <!-- Formulario de solicitud -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Formulario de Solicitud</h2>
        
        <form action="/solicitudes" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="propiedad_id" value="<?= $propiedad['id'] ?>">
            
            <!-- Mensaje -->
            <div>
                <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-2">
                    Mensaje para el agente <span class="text-gray-500">(opcional)</span>
                </label>
                <textarea 
                    id="mensaje" 
                    name="mensaje" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Describe tu interés en la propiedad, preguntas específicas, o cualquier información adicional que consideres importante..."
                ></textarea>
                <p class="mt-1 text-sm text-gray-500">
                    Cuéntanos más sobre tu interés en esta propiedad
                </p>
            </div>



            <!-- Información del cliente -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Información de contacto que se enviará al agente:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Nombre:</span>
                        <span class="text-gray-900"><?= htmlspecialchars(($_SESSION['user_nombre'] ?? 'No especificado') . ' ' . ($_SESSION['user_apellido'] ?? '')) ?></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Email:</span>
                        <span class="text-gray-900"><?= htmlspecialchars($_SESSION['user_email'] ?? 'No especificado') ?></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Teléfono:</span>
                        <span class="text-gray-900"><?= htmlspecialchars($_SESSION['user_telefono'] ?? 'No especificado') ?></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Ciudad:</span>
                        <span class="text-gray-900"><?= htmlspecialchars($_SESSION['user_ciudad'] ?? 'No especificado') ?></span>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a 
                    href="/properties/show/<?= $propiedad['id'] ?>" 
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    Cancelar
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    Enviar Solicitud
                </button>
            </div>
        </form>
    </div>

    <!-- Información adicional -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">¿Qué pasa después?</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>El agente recibirá tu solicitud y se pondrá en contacto contigo</li>
                        <li>Podrás chatear directamente con el agente desde la plataforma</li>
                        <li>El agente podrá agendar citas para visitar la propiedad</li>
                        <li>Recibirás notificaciones por email sobre el estado de tu solicitud</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>

<script src="/js/solicitudes.js"></script>

 