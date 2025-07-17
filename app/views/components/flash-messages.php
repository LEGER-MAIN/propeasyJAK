<?php
/**
 * Componente: Mensajes Flash
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este componente muestra los mensajes flash (éxito, error, info, warning)
 * que se establecen durante la ejecución de la aplicación
 */

// Obtener mensajes flash de la sesión
$flashMessages = $_SESSION['flash_messages'] ?? [];
unset($_SESSION['flash_messages']); // Limpiar mensajes después de mostrarlos

// Si no hay mensajes, no mostrar nada
if (empty($flashMessages)) {
    return;
}
?>

<div id="flash-messages" class="fixed top-4 right-4 z-50 space-y-2">
    <?php foreach ($flashMessages as $message): ?>
        <div class="flash-message max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden transform transition-all duration-300 ease-in-out"
             data-type="<?= htmlspecialchars($message['type']) ?>">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <?php if ($message['type'] === 'success'): ?>
                            <i class="fas fa-check-circle text-green-400 text-lg"></i>
                        <?php elseif ($message['type'] === 'error'): ?>
                            <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                        <?php elseif ($message['type'] === 'warning'): ?>
                            <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
                        <?php else: ?>
                            <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                        <?php endif; ?>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">
                            <?= htmlspecialchars($message['message']) ?>
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Cerrar</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-ocultar mensajes después de 5 segundos
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.transform = 'translateX(100%)';
            setTimeout(function() {
                message.remove();
            }, 300);
        }, 5000);
    });
    
    // Permitir cerrar mensajes manualmente
    document.addEventListener('click', function(e) {
        if (e.target.closest('.flash-message button')) {
            const message = e.target.closest('.flash-message');
            message.style.transform = 'translateX(100%)';
            setTimeout(function() {
                message.remove();
            }, 300);
        }
    });
});
</script>

<style>
.flash-message {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Colores específicos para cada tipo de mensaje */
.flash-message[data-type="success"] {
    border-left: 4px solid #10b981;
}

.flash-message[data-type="error"] {
    border-left: 4px solid #ef4444;
}

.flash-message[data-type="warning"] {
    border-left: 4px solid #f59e0b;
}

.flash-message[data-type="info"] {
    border-left: 4px solid #3b82f6;
}

.flash-message, .toast, .alert, .notification {
    max-width: 500px;
    min-width: 250px;
    padding: 12px 24px;
    white-space: normal;
    word-break: break-word;
    z-index: 9999;
    box-sizing: border-box;
}
</style> 