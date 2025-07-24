<?php
$pageTitle = 'Chat con ' . htmlspecialchars($agente['nombre'] . ' ' . $agente['apellido']) . ' - ' . APP_NAME;
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header de la Conversación -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/properties/show/<?= $_GET['property'] ?? '' ?>" class="text-primary-600 hover:text-primary-700 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xl font-bold">
                                    <?= strtoupper(substr($agente['nombre'], 0, 1)) ?>
                                </span>
                            </div>
                            <div id="user-status-indicator" class="w-3 h-3 bg-green-500 rounded-full absolute -bottom-1 -right-1 border-2 border-white"></div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                <?= htmlspecialchars($agente['nombre'] . ' ' . $agente['apellido']) ?>
                            </h1>
                            <p class="text-gray-600">Agente Inmobiliario</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div id="connection-status" class="flex items-center space-x-2">
                        <div id="status-indicator" class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span id="status-text" class="text-sm text-gray-600">Conectado</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor del Chat -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="flex flex-col h-[600px]">
                <!-- Header de la Conversación -->
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-bold">
                                    <?= strtoupper(substr($agente['nombre'], 0, 1)) ?>
                                </span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    <?= htmlspecialchars($agente['nombre'] . ' ' . $agente['apellido']) ?>
                                </h3>
                                <p id="conversation-status" class="text-sm text-gray-600">Agente disponible</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div id="typing-indicator" class="text-sm text-gray-500 hidden">
                                <i class="fas fa-pencil-alt mr-1"></i>
                                Escribiendo...
                            </div>
                            <div id="user-status" class="w-2 h-2 bg-green-500 rounded-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Área de Mensajes -->
                <div id="messages-container" class="flex-1 overflow-y-auto p-4 bg-gray-50">
                    <?php if (empty($mensajes)): ?>
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-comments text-4xl mb-2"></i>
                            <p>Inicia la conversación con <?= htmlspecialchars($agente['nombre']) ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($mensajes as $mensaje): ?>
                            <div class="mb-4 <?= $mensaje['user_id'] == $user_id ? 'text-right' : 'text-left' ?>">
                                <div class="inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg <?= $mensaje['user_id'] == $user_id ? 'bg-primary-600 text-white' : 'bg-white text-gray-900 border border-gray-200' ?>">
                                    <p class="text-sm"><?= htmlspecialchars($mensaje['mensaje']) ?></p>
                                    <p class="text-xs mt-1 <?= $mensaje['user_id'] == $user_id ? 'text-primary-100' : 'text-gray-500' ?>">
                                        <?= date('H:i', strtotime($mensaje['fecha_creacion'])) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Input de Mensaje -->
                <div id="message-input-container" class="p-4 border-t border-gray-200 bg-white">
                    <div class="flex space-x-3">
                        <div class="flex-1 relative">
                            <textarea id="message-input" rows="1" placeholder="Escribe tu mensaje..." 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"></textarea>
                        </div>
                        <button id="send-message" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let conversationId = <?= $conversacion_id ?>;
let userId = <?= $user_id ?>;
let agentId = <?= $agente['id'] ?>;

// Elementos del DOM
const messagesContainer = document.getElementById('messages-container');
const messageInput = document.getElementById('message-input');
const sendButton = document.getElementById('send-message');
const typingIndicator = document.getElementById('typing-indicator');

// Auto-resize del textarea
messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Enviar mensaje con Enter
messageInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// Enviar mensaje con botón
sendButton.addEventListener('click', sendMessage);

function sendMessage() {
    const message = messageInput.value.trim();
    if (!message) return;

    // Deshabilitar botón temporalmente
    sendButton.disabled = true;
    
    // Agregar mensaje localmente
    addMessageToChat(message, userId, true);
    
    // Limpiar input
    messageInput.value = '';
    messageInput.style.height = 'auto';

    // Enviar mensaje al servidor
    fetch('/chat/send-direct-message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            message: message,
            recipient_id: agentId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error al enviar mensaje:', data.error);
            // Mostrar error al usuario
            showError('Error al enviar mensaje. Inténtalo de nuevo.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error de conexión. Inténtalo de nuevo.');
    })
    .finally(() => {
        sendButton.disabled = false;
    });
}

function addMessageToChat(message, senderId, isOwn = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `mb-4 ${isOwn ? 'text-right' : 'text-left'}`;
    
    const messageBubble = document.createElement('div');
    messageBubble.className = `inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${isOwn ? 'bg-primary-600 text-white' : 'bg-white text-gray-900 border border-gray-200'}`;
    
    const messageText = document.createElement('p');
    messageText.className = 'text-sm';
    messageText.textContent = message;
    
    const messageTime = document.createElement('p');
    messageTime.className = `text-xs mt-1 ${isOwn ? 'text-primary-100' : 'text-gray-500'}`;
    messageTime.textContent = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    
    messageBubble.appendChild(messageText);
    messageBubble.appendChild(messageTime);
    messageDiv.appendChild(messageBubble);
    
    messagesContainer.appendChild(messageDiv);
    scrollToBottom();
}

function scrollToBottom() {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function showError(message) {
    // Crear notificación de error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    errorDiv.textContent = message;
    
    document.body.appendChild(errorDiv);
    
    setTimeout(() => {
        errorDiv.remove();
    }, 3000);
}

// Cargar mensajes al iniciar
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    
    // Polling para nuevos mensajes cada 3 segundos
    setInterval(loadNewMessages, 3000);
});

function loadNewMessages() {
    fetch(`/chat/direct/${conversationId}/messages`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.messages) {
            // Aquí podrías implementar la lógica para mostrar solo mensajes nuevos
            // Por simplicidad, recargamos todos los mensajes
            updateMessages(data.messages);
        }
    })
    .catch(error => {
        console.error('Error cargando mensajes:', error);
    });
}

function updateMessages(messages) {
    // Limpiar contenedor
    messagesContainer.innerHTML = '';
    
    if (messages.length === 0) {
        messagesContainer.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-comments text-4xl mb-2"></i>
                <p>Inicia la conversación con <?= htmlspecialchars($agente['nombre']) ?></p>
            </div>
        `;
        return;
    }
    
    // Agregar mensajes
    messages.forEach(mensaje => {
        const isOwn = mensaje.user_id == userId;
        addMessageToChat(mensaje.mensaje, mensaje.user_id, isOwn);
    });
}
</script> 
