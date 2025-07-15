<?php
$pageTitle = 'Chat - ' . APP_NAME;
?>

<div class="min-h-screen bg-gray-100">
    <div class="max-w-6xl mx-auto h-screen flex">
        <!-- Sidebar de Contactos -->
        <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
            <!-- Header del Sidebar -->
            <div class="p-4 border-b border-gray-200 bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-xl font-bold text-gray-900">Chat</h1>
                    <div class="flex items-center space-x-2">
                        <button id="new-chat-btn" class="w-8 h-8 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors flex items-center justify-center">
                            <i class="fas fa-plus text-sm"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Buscador -->
                <div class="relative">
                    <input type="text" id="search-users" placeholder="Buscar usuarios..." 
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Lista de Conversaciones -->
            <div id="conversations-list" class="flex-1 overflow-y-auto">
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-comments text-4xl mb-2"></i>
                    <p>Busca usuarios para comenzar a chatear</p>
                </div>
            </div>
        </div>

        <!-- Área de Chat -->
        <div class="flex-1 flex flex-col bg-gray-50">
            <!-- Header del Chat -->
            <div id="chat-header" class="p-4 border-b border-gray-200 bg-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <div>
                            <h3 id="chat-title" class="font-semibold text-gray-900">Selecciona un chat</h3>
                            <p id="chat-subtitle" class="text-sm text-gray-600">Para comenzar a conversar</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Área de Mensajes -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-4 bg-gray-50">
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-comment-dots text-4xl mb-2"></i>
                    <p>Selecciona un chat para comenzar</p>
                </div>
            </div>

            <!-- Input de Mensaje -->
            <div id="message-input-container" class="p-4 border-t border-gray-200 bg-white hidden">
                <div class="flex space-x-3">
                    <div class="flex-1">
                        <textarea id="message-input" rows="1" placeholder="Escribe un mensaje..." 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"></textarea>
                    </div>
                    <button id="send-message" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Buscar Usuarios -->
<div id="search-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Buscar Usuario</h3>
                    <button id="close-search-modal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <input type="text" id="modal-search-input" placeholder="Buscar por nombre o email..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    
                    <div id="search-results" class="max-h-64 overflow-y-auto">
                        <!-- Resultados de búsqueda se cargarán aquí -->
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button id="cancel-search" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// CHAT COMPLETAMENTE NUEVO DESDE CERO - ESTILO WHATSAPP
console.log('🚀 CARGANDO CHAT NUEVO DESDE CERO');

// Variables globales
let currentChat = null;
let conversations = [];
let searchTimeout = null;

// Elementos del DOM
const elements = {
    searchUsers: document.getElementById('search-users'),
    conversationsList: document.getElementById('conversations-list'),
    chatTitle: document.getElementById('chat-title'),
    chatSubtitle: document.getElementById('chat-subtitle'),
    messagesContainer: document.getElementById('messages-container'),
    messageInputContainer: document.getElementById('message-input-container'),
    messageInput: document.getElementById('message-input'),
    sendMessage: document.getElementById('send-message'),
    newChatBtn: document.getElementById('new-chat-btn'),
    searchModal: document.getElementById('search-modal'),
    modalSearchInput: document.getElementById('modal-search-input'),
    searchResults: document.getElementById('search-results'),
    closeSearchModal: document.getElementById('close-search-modal'),
    cancelSearch: document.getElementById('cancel-search')
};

// FUNCIÓN PRINCIPAL DE ENVÍO DE MENSAJES
function sendMessage() {
    const messageInput = document.getElementById('message-input');
    const content = messageInput.value.trim();
    
    if (!content) {
        alert('Escribe un mensaje');
        return;
    }
    
    if (!currentChat) {
        alert('Selecciona un chat');
        return;
    }
    
    console.log('Enviando mensaje a:', currentChat.user_id);
    
    // Enviar mensaje
    fetch('/chat/send-direct-message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            conversation_id: currentChat.conversation_id,
            message: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Mensaje enviado');
            
            // Limpiar input
            messageInput.value = '';
            
            // Agregar mensaje a la interfaz
            addMessageToUI(content, true);
            
            // Actualizar último mensaje en la lista
            updateLastMessage(currentChat.conversation_id, content);
            
        } else {
            console.error('❌ Error:', data.error);
            alert('Error: ' + (data.error || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        alert('Error al enviar mensaje: ' + error.message);
    });
}

// AGREGAR MENSAJE A LA INTERFAZ
function addMessageToUI(content, isOwn = false) {
    const messagesContainer = document.getElementById('messages-container');
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `mb-4 ${isOwn ? 'flex justify-end' : 'flex justify-start'}`;
    
    messageDiv.innerHTML = `
        <div class="${isOwn ? 'bg-green-500 text-white' : 'bg-white text-gray-900'} px-4 py-2 rounded-lg max-w-xs shadow-sm">
            <p>${content}</p>
            <p class="text-xs opacity-75 mt-1">${new Date().toLocaleTimeString()}</p>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// CARGAR CONVERSACIONES
async function loadConversations() {
    try {
        console.log('Cargando conversaciones...');
        
        const response = await fetch('/chat/direct-conversations');
        const data = await response.json();
        
        if (data.success) {
            conversations = data.conversations;
            console.log('Conversaciones cargadas:', conversations);
            renderConversations();
        }
    } catch (error) {
        console.error('Error cargando conversaciones:', error);
    }
}

// RENDERIZAR CONVERSACIONES
function renderConversations() {
    if (conversations.length === 0) {
        elements.conversationsList.innerHTML = `
            <div class="p-4 text-center text-gray-500">
                <i class="fas fa-comments text-4xl mb-2"></i>
                <p>No hay conversaciones</p>
                <p class="text-sm mt-2">Busca usuarios para comenzar</p>
            </div>
        `;
        return;
    }
    
    elements.conversationsList.innerHTML = conversations.map(conv => `
        <div class="conversation-item p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors ${conv.conversacion_id === currentChat?.conversacion_id ? 'bg-green-50 border-green-200' : ''}" 
             data-conversation-id="${conv.conversacion_id}" data-user-id="${conv.cliente_id === <?= $_SESSION['user_id'] ?? 0 ?> ? conv.agente_id : conv.cliente_id}">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-medium text-gray-900 truncate">${conv.nombre_otro_usuario} ${conv.apellido_otro_usuario}</h4>
                    <p class="text-sm text-gray-500 truncate">${conv.ultimo_mensaje || 'Sin mensajes'}</p>
                </div>
                <div class="flex flex-col items-end space-y-1">
                    <span class="text-xs text-gray-400">${formatTime(conv.fecha_ultimo_mensaje)}</span>
                    ${conv.mensajes_no_leidos > 0 ? `<span class="bg-green-500 text-white text-xs rounded-full px-2 py-1">${conv.mensajes_no_leidos}</span>` : ''}
                </div>
            </div>
        </div>
    `).join('');
    
    // Agregar event listeners
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const conversationId = this.dataset.conversationId;
            const userId = this.dataset.userId;
            selectConversation(conversationId, userId);
        });
    });
}

// SELECCIONAR CONVERSACIÓN
function selectConversation(conversationId, userId) {
    currentChat = conversations.find(c => c.conversacion_id == conversationId);
    if (!currentChat) return;
    
    currentChat.user_id = userId;
    
    console.log('Seleccionando conversación:', conversationId, 'Usuario:', userId);
    
    // Actualizar UI
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('bg-green-50', 'border-green-200');
    });
    document.querySelector(`[data-conversation-id="${conversationId}"]`).classList.add('bg-green-50', 'border-green-200');
    
    // Actualizar header
    elements.chatTitle.textContent = `${currentChat.nombre_otro_usuario} ${currentChat.apellido_otro_usuario}`;
    elements.chatSubtitle.textContent = 'En línea';
    
    // Mostrar área de mensajes
    elements.messageInputContainer.classList.remove('hidden');
    
    // Cargar mensajes
    loadMessages(conversationId);
}

// CARGAR MENSAJES
async function loadMessages(conversationId) {
    try {
        console.log('Cargando mensajes para conversación:', conversationId);
        
        const response = await fetch(`/chat/direct/${conversationId}/messages`);
        const data = await response.json();
        
        if (data.success) {
            console.log('Mensajes cargados:', data.messages);
            renderMessages(data.messages);
        }
    } catch (error) {
        console.error('Error cargando mensajes:', error);
    }
}

// RENDERIZAR MENSAJES
function renderMessages(messages) {
    elements.messagesContainer.innerHTML = '';
    
    messages.forEach(msg => {
        const isOwnMessage = msg.remitente_id == <?= $_SESSION['user_id'] ?? 0 ?>;
        const messageContent = msg.mensaje || 'Mensaje sin contenido';
        
        addMessageToUI(messageContent, isOwnMessage);
    });
}

// BUSCAR USUARIOS
async function searchUsers(query) {
    if (!query.trim()) {
        elements.searchResults.innerHTML = '';
        return;
    }
    
    try {
        const response = await fetch(`/chat/users-for-direct-chat?search=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.success) {
            renderSearchResults(data.users);
        }
    } catch (error) {
        console.error('Error buscando usuarios:', error);
    }
}

// RENDERIZAR RESULTADOS DE BÚSQUEDA
function renderSearchResults(users) {
    if (users.length === 0) {
        elements.searchResults.innerHTML = `
            <div class="text-center text-gray-500 py-4">
                <p>No se encontraron usuarios</p>
            </div>
        `;
        return;
    }
    
    elements.searchResults.innerHTML = users.map(user => `
        <div class="user-item p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors" 
             data-user-id="${user.id}" data-user-name="${user.nombre} ${user.apellido}">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900">${user.nombre} ${user.apellido}</h4>
                    <p class="text-sm text-gray-500">${user.email}</p>
                </div>
                <div class="text-xs text-gray-400">
                    ${user.online ? '<span class="text-green-500">●</span> En línea' : 'Desconectado'}
                </div>
            </div>
        </div>
    `).join('');
    
    // Agregar event listeners
    document.querySelectorAll('.user-item').forEach(item => {
        item.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            startConversation(userId, userName);
        });
    });
}

// INICIAR CONVERSACIÓN
async function startConversation(userId, userName) {
    try {
        const response = await fetch('/chat/create-direct-conversation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: userId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            console.log('Conversación creada:', data.conversation);
            
            // Cerrar modal
            elements.searchModal.classList.add('hidden');
            elements.modalSearchInput.value = '';
            
            // Recargar conversaciones
            await loadConversations();
            
            // Seleccionar la nueva conversación
            selectConversation(data.conversation.conversacion_id, userId);
            
        } else {
            alert('Error: ' + (data.error || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error creando conversación:', error);
        alert('Error al crear conversación: ' + error.message);
    }
}

// ACTUALIZAR ÚLTIMO MENSAJE
function updateLastMessage(conversationId, message) {
    const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
    if (conversationItem) {
        const messageElement = conversationItem.querySelector('p');
        if (messageElement) {
            messageElement.textContent = message;
        }
    }
}

// FORMATEAR TIEMPO
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

// EVENT LISTENERS
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM cargado, configurando chat nuevo...');
    
    // Cargar conversaciones iniciales
    loadConversations();
    
    // BOTÓN ENVIAR
    elements.sendMessage.addEventListener('click', function(e) {
        e.preventDefault();
        sendMessage();
    });
    
    // INPUT ENTER
    elements.messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // BOTÓN NUEVO CHAT
    elements.newChatBtn.addEventListener('click', function() {
        elements.searchModal.classList.remove('hidden');
        elements.modalSearchInput.focus();
    });
    
    // CERRAR MODAL
    elements.closeSearchModal.addEventListener('click', function() {
        elements.searchModal.classList.add('hidden');
    });
    
    elements.cancelSearch.addEventListener('click', function() {
        elements.searchModal.classList.add('hidden');
    });
    
    // BUSCAR EN MODAL
    elements.modalSearchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Limpiar timeout anterior
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        // Buscar después de 300ms de inactividad
        searchTimeout = setTimeout(() => {
            searchUsers(query);
        }, 300);
    });
    
    // BUSCAR EN SIDEBAR
    elements.searchUsers.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Filtrar conversaciones existentes
        const conversationItems = document.querySelectorAll('.conversation-item');
        conversationItems.forEach(item => {
            const userName = item.querySelector('h4').textContent.toLowerCase();
            if (userName.includes(query.toLowerCase())) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    console.log('🚀 CHAT NUEVO LISTO');
});
</script> 