<?php
$pageTitle = 'Conversación - ' . APP_NAME;
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header de la Conversación -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/chat" class="text-primary-600 hover:text-primary-700 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                            <div id="user-status-indicator" class="w-3 h-3 bg-gray-400 rounded-full absolute -bottom-1 -right-1 border-2 border-white"></div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($conversacion['nombre_otro_usuario'] ?? 'Usuario') ?></h1>
                            <p id="user-status-text" class="text-gray-600">Desconectado</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div id="connection-status" class="flex items-center space-x-2">
                        <div id="status-indicator" class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span id="status-text" class="text-sm text-gray-600">Desconectado</span>
                    </div>
                    <button id="reconnect-btn" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors hidden">
                        Reconectar
                    </button>
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
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($conversacion['nombre_otro_usuario'] ?? 'Usuario') ?></h3>
                                <p id="conversation-status" class="text-sm text-gray-600">Cargando...</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div id="typing-indicator" class="text-sm text-gray-500 hidden">
                                <i class="fas fa-pencil-alt mr-1"></i>
                                Escribiendo...
                            </div>
                            <div id="user-status" class="w-2 h-2 bg-gray-400 rounded-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Área de Mensajes -->
                <div id="messages-container" class="flex-1 overflow-y-auto p-4 bg-gray-50">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-spinner fa-spin text-4xl mb-2"></i>
                        <p>Cargando mensajes...</p>
                    </div>
                </div>

                <!-- Input de Mensaje -->
                <div id="message-input-container" class="p-4 border-t border-gray-200 bg-white">
                    <div class="flex space-x-3">
                        <div class="flex-1 relative">
                            <textarea id="message-input" rows="1" placeholder="Escribe tu mensaje..." 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"></textarea>
                            <button id="attach-file" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-paperclip"></i>
                            </button>
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
// Configuración del WebSocket
const WS_HOST = window.location.hostname;
const WS_PORT = 8080;
const WS_URL = '<?= function_exists("getWebSocketUrl") ? getWebSocketUrl() : "ws://localhost:8080" ?>';

// Variables globales
let ws = null;
let conversationId = <?= $solicitud_id ?? 0 ?>;
let reconnectAttempts = 0;
const maxReconnectAttempts = 5;
let typingTimeout = null;

// Elementos del DOM
const elements = {
    statusIndicator: document.getElementById('status-indicator'),
    statusText: document.getElementById('status-text'),
    reconnectBtn: document.getElementById('reconnect-btn'),
    userStatusIndicator: document.getElementById('user-status-indicator'),
    userStatusText: document.getElementById('user-status-text'),
    conversationStatus: document.getElementById('conversation-status'),
    typingIndicator: document.getElementById('typing-indicator'),
    userStatus: document.getElementById('user-status'),
    messagesContainer: document.getElementById('messages-container'),
    messageInput: document.getElementById('message-input'),
    sendMessage: document.getElementById('send-message')
};

// Inicializar WebSocket
function initWebSocket() {
    try {
        ws = new WebSocket(WS_URL);
        
        ws.onopen = function() {
            console.log('WebSocket conectado');
            updateConnectionStatus(true);
            reconnectAttempts = 0;
            
            // Enviar información del usuario
            ws.send(JSON.stringify({
                type: 'auth',
                userId: <?= $_SESSION['user_id'] ?? 0 ?>,
                userName: '<?= $_SESSION['user_nombre'] ?? 'Usuario' ?>',
                userRole: '<?= $_SESSION['role'] ?? 'cliente' ?>'
            }));
            
            // Cargar mensajes de la conversación
            loadMessages();
        };
        
        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            handleWebSocketMessage(data);
        };
        
        ws.onclose = function() {
            console.log('WebSocket desconectado');
            updateConnectionStatus(false);
            
            // Intentar reconectar
            if (reconnectAttempts < maxReconnectAttempts) {
                setTimeout(() => {
                    reconnectAttempts++;
                    initWebSocket();
                }, 2000 * reconnectAttempts);
            } else {
                showReconnectButton();
            }
        };
        
        ws.onerror = function(error) {
            console.error('Error en WebSocket:', error);
            updateConnectionStatus(false);
        };
        
    } catch (error) {
        console.error('Error al conectar WebSocket:', error);
        updateConnectionStatus(false);
    }
}

// Actualizar estado de conexión
function updateConnectionStatus(connected) {
    if (connected) {
        elements.statusIndicator.className = 'w-3 h-3 bg-green-500 rounded-full';
        elements.statusText.textContent = 'Conectado';
        elements.reconnectBtn.classList.add('hidden');
    } else {
        elements.statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full';
        elements.statusText.textContent = 'Desconectado';
        elements.reconnectBtn.classList.remove('hidden');
    }
}

// Mostrar botón de reconexión
function showReconnectButton() {
    elements.reconnectBtn.classList.remove('hidden');
    elements.reconnectBtn.onclick = function() {
        reconnectAttempts = 0;
        initWebSocket();
    };
}

// Manejar mensajes del WebSocket
function handleWebSocketMessage(data) {
    switch (data.type) {
        case 'messages':
            renderMessages(data.messages);
            break;
            
        case 'message':
            handleNewMessage(data.message);
            break;
            
        case 'typing':
            handleTypingIndicator(data);
            break;
            
        case 'user_status':
            handleUserStatus(data);
            break;
            
        case 'conversation_info':
            updateConversationInfo(data.info);
            break;
    }
}

// Cargar mensajes
function loadMessages() {
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            type: 'get_messages',
            conversationId: conversationId
        }));
    }
}

// Renderizar mensajes
function renderMessages(messages) {
    if (messages.length === 0) {
        elements.messagesContainer.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-comment-dots text-4xl mb-2"></i>
                <p>No hay mensajes en esta conversación</p>
                <p class="text-sm">¡Sé el primero en enviar un mensaje!</p>
            </div>
        `;
        return;
    }
    
    elements.messagesContainer.innerHTML = messages.map(message => {
        const isOwnMessage = message.sender_id == <?= $_SESSION['user_id'] ?? 0 ?>;
        return `
            <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-4">
                <div class="max-w-xs lg:max-w-md">
                    <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-1">
                        <span class="text-xs text-gray-500">${message.sender_name}</span>
                    </div>
                    <div class="bg-${isOwnMessage ? 'primary-600 text-white' : 'white border border-gray-200'} rounded-lg px-4 py-2 shadow-sm">
                        <p class="text-sm">${message.message}</p>
                    </div>
                    <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mt-1">
                        <span class="text-xs text-gray-400">${formatTime(message.created_at)}</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    elements.messagesContainer.scrollTop = elements.messagesContainer.scrollHeight;
}

// Manejar nuevo mensaje
function handleNewMessage(message) {
    const isOwnMessage = message.sender_id == <?= $_SESSION['user_id'] ?? 0 ?>;
    const messageElement = document.createElement('div');
    messageElement.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-4`;
    messageElement.innerHTML = `
        <div class="max-w-xs lg:max-w-md">
            <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-1">
                <span class="text-xs text-gray-500">${message.sender_name}</span>
            </div>
            <div class="bg-${isOwnMessage ? 'primary-600 text-white' : 'white border border-gray-200'} rounded-lg px-4 py-2 shadow-sm">
                <p class="text-sm">${message.message}</p>
            </div>
            <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mt-1">
                <span class="text-xs text-gray-400">${formatTime(message.created_at)}</span>
            </div>
        </div>
    `;
    
    elements.messagesContainer.appendChild(messageElement);
    elements.messagesContainer.scrollTop = elements.messagesContainer.scrollHeight;
}

// Manejar indicador de escritura
function handleTypingIndicator(data) {
    if (data.typing) {
        elements.typingIndicator.classList.remove('hidden');
    } else {
        elements.typingIndicator.classList.add('hidden');
    }
}

// Manejar estado de usuario
function handleUserStatus(data) {
    const isOnline = data.online;
    elements.userStatusIndicator.className = `w-3 h-3 ${isOnline ? 'bg-green-500' : 'bg-gray-400'} rounded-full absolute -bottom-1 -right-1 border-2 border-white`;
    elements.userStatusText.textContent = isOnline ? 'En línea' : 'Desconectado';
    elements.userStatus.className = `w-2 h-2 ${isOnline ? 'bg-green-500' : 'bg-gray-400'} rounded-full`;
}

// Actualizar información de la conversación
function updateConversationInfo(info) {
    elements.conversationStatus.textContent = info.status || 'Activa';
}

// Enviar mensaje
function sendMessage() {
    const content = elements.messageInput.value.trim();
    if (!content) return;
    
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            type: 'message',
            conversationId: conversationId,
            content: content,
            senderId: <?= $_SESSION['user_id'] ?? 0 ?>,
            senderName: '<?= $_SESSION['user_nombre'] ?? 'Usuario' ?>'
        }));
        
        elements.messageInput.value = '';
        elements.messageInput.style.height = 'auto';
    }
}

// Enviar indicador de escritura
function sendTypingIndicator(typing) {
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            type: 'typing',
            conversationId: conversationId,
            typing: typing,
            userId: <?= $_SESSION['user_id'] ?? 0 ?>
        }));
    }
}

// Formatear tiempo
function formatTime(timestamp) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    
    if (diff < 60000) return 'Ahora';
    if (diff < 3600000) return `${Math.floor(diff / 60000)}m`;
    if (diff < 86400000) return `${Math.floor(diff / 3600000)}h`;
    return date.toLocaleDateString();
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar WebSocket
    initWebSocket();
    
    // Enviar mensaje
    elements.sendMessage.addEventListener('click', sendMessage);
    elements.messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Auto-resize textarea
    elements.messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
        
        // Enviar indicador de escritura
        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }
        
        sendTypingIndicator(true);
        
        typingTimeout = setTimeout(() => {
            sendTypingIndicator(false);
        }, 1000);
    });
    
    // Obtener información de la conversación
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            type: 'get_conversation_info',
            conversationId: conversationId
        }));
    }
});
</script> 