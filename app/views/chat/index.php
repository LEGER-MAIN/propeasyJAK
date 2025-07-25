<?php
$pageTitle = 'Chat - ' . APP_NAME;
?>

<!-- CHAT PRINCIPAL CON LAYOUT COMPLETO -->
<div class="min-h-screen bg-gray-100">
    <div class="max-w-6xl mx-auto h-[calc(100vh-200px)] flex">
        <!-- Sidebar de Conversaciones -->
        <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
            <!-- Header del Sidebar -->
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Conversaciones</h2>
                <button id="new-chat-btn" class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nueva Conversación
                </button>
            </div>
            
            <!-- Lista de Conversaciones -->
            <div id="conversations-list" class="flex-1 overflow-y-auto">
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>Cargando conversaciones...</p>
                </div>
            </div>
        </div>

        <!-- Área de Chat -->
        <div class="flex-1 flex flex-col bg-gray-50">
            <!-- Header del Chat -->
            <div id="chat-header" class="p-4 border-b border-gray-200 bg-white">
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
    // ===== CHAT PRINCIPAL DESDE CERO =====
    console.log('🚀 INICIANDO CHAT PRINCIPAL DESDE CERO');

    // Variables globales
    let currentChat = null;
    let conversations = [];
    let websocket = null;
    let isWebSocketConnected = false;
    let searchTimeout = null;

    // Elementos del DOM
    const elements = {
        wsStatus: document.getElementById('ws-status'),
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

    // ===== FUNCIONES PRINCIPALES =====

    // FUNCIÓN DE ENVÍO DE MENSAJES
    async function sendMessage() {
        console.log('🚀 Función sendMessage ejecutada');
        console.log('🔍 Elementos del DOM:', elements);
        console.log('🔍 currentChat:', currentChat);
        
        const content = elements.messageInput.value.trim();
        console.log('📝 Contenido del mensaje:', content);
        
        if (!content) {
            console.log('❌ Mensaje vacío');
            alert('Escribe un mensaje');
            return;
        }
        
        if (!currentChat) {
            console.log('❌ No hay chat seleccionado');
            alert('Selecciona un chat');
            return;
        }
        
        console.log('🚀 Enviando mensaje...');
        console.log('📝 Conversación:', currentChat.conversacion_id);
        console.log('💬 Mensaje:', content);
        
        try {
            const response = await fetch('/chat/send-direct-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    conversation_id: currentChat.conversacion_id,
                    message: content
                })
            });
            
            console.log('📥 Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('📥 Response data:', data);
            
            if (data.success) {
                console.log('✅ Mensaje enviado exitosamente');
                
                // Limpiar input
                elements.messageInput.value = '';
                
                // Agregar mensaje a la interfaz
                addMessageToUI(content, true);
                
                // Actualizar último mensaje en la lista
                updateLastMessage(currentChat.conversacion_id, content);
                
            } else {
                console.error('❌ Error:', data.error);
                alert('Error: ' + (data.error || 'Error desconocido'));
            }
        } catch (error) {
            console.error('❌ Error en fetch:', error);
            alert('Error al enviar mensaje: ' + error.message);
        }
    }

    // AGREGAR MENSAJE A LA INTERFAZ
    function addMessageToUI(content, isOwn = false, timestamp = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-4 ${isOwn ? 'flex justify-end' : 'flex justify-start'}`;
        
        const time = timestamp || new Date().toLocaleTimeString();
        
        messageDiv.innerHTML = `
            <div class="${isOwn ? 'bg-green-500 text-white' : 'bg-white text-gray-900'} px-4 py-2 rounded-lg max-w-xs shadow-sm">
                <p>${content}</p>
                <p class="text-xs opacity-75 mt-1">${time}</p>
            </div>
        `;
        
        elements.messagesContainer.appendChild(messageDiv);
        elements.messagesContainer.scrollTop = elements.messagesContainer.scrollHeight;
    }

    // CARGAR CONVERSACIONES
    async function loadConversations() {
        try {
            console.log('📥 Cargando conversaciones...');
            
            const response = await fetch('/chat/direct-conversations', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log('📥 Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('📥 Response data:', data);
            
            if (data.success) {
                conversations = data.conversations;
                console.log('✅ Conversaciones cargadas:', conversations);
                renderConversations();
            } else {
                console.error('❌ Error en respuesta:', data.error);
                showError('Error cargando conversaciones: ' + data.error);
            }
        } catch (error) {
            console.error('❌ Error cargando conversaciones:', error);
            showError('Error cargando conversaciones: ' + error.message);
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
        
        console.log('✅ Seleccionando conversación:', conversationId, 'Usuario:', userId);
        
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
            console.log('📥 Cargando mensajes para conversación:', conversationId);
            
            const response = await fetch(`/chat/direct/${conversationId}/messages`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log('📥 Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('📥 Response data:', data);
            
            if (data.success) {
                console.log('✅ Mensajes cargados:', data.messages);
                renderMessages(data.messages);
            } else {
                console.error('❌ Error en respuesta:', data.error);
                showError('Error cargando mensajes: ' + data.error);
            }
        } catch (error) {
            console.error('❌ Error cargando mensajes:', error);
            showError('Error cargando mensajes: ' + error.message);
        }
    }

    // RENDERIZAR MENSAJES
    function renderMessages(messages) {
        elements.messagesContainer.innerHTML = '';
        
        if (messages.length === 0) {
            elements.messagesContainer.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-comment-dots text-4xl mb-2"></i>
                    <p>No hay mensajes</p>
                    <p class="text-sm mt-2">Sé el primero en escribir</p>
                </div>
            `;
            return;
        }
        
        messages.forEach(msg => {
            const isOwnMessage = msg.remitente_id == <?= $_SESSION['user_id'] ?? 0 ?>;
            const messageContent = msg.mensaje || 'Mensaje sin contenido';
            const timestamp = msg.fecha_envio ? new Date(msg.fecha_envio).toLocaleTimeString() : new Date().toLocaleTimeString();
            
            addMessageToUI(messageContent, isOwnMessage, timestamp);
        });
    }

    // BUSCAR USUARIOS
    async function searchUsers(query) {
        if (!query.trim()) {
            elements.searchResults.innerHTML = '';
            return;
        }
        
        try {
            console.log('🔍 Buscando usuarios con query:', query);
            
            const response = await fetch(`/chat/search-users?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('📥 Usuarios encontrados:', data);
            
            if (data.success) {
                renderSearchResults(data.users);
            } else {
                console.error('❌ Error en búsqueda:', data.error);
            }
        } catch (error) {
            console.error('❌ Error buscando usuarios:', error);
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
                    <div>
                        <h4 class="font-medium text-gray-900">${user.nombre} ${user.apellido}</h4>
                        <p class="text-sm text-gray-500">${user.email}</p>
                    </div>
                </div>
            </div>
        `).join('');
        
        // Agregar event listeners
        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', function() {
                const userId = this.dataset.userId;
                const userName = this.dataset.userName;
                createConversation(userId, userName);
            });
        });
    }

    // CREAR CONVERSACIÓN
    async function createConversation(userId, userName) {
        try {
            console.log('🚀 Creando conversación con usuario:', userId);
            
            const response = await fetch('/chat/create-direct-conversation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    user_id: userId
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('📥 Response data:', data);
            
            if (data.success) {
                console.log('✅ Conversación creada');
                elements.searchModal.classList.add('hidden');
                loadConversations(); // Recargar conversaciones
            } else {
                console.error('❌ Error:', data.error);
                alert('Error: ' + (data.error || 'Error desconocido'));
            }
        } catch (error) {
            console.error('❌ Error creando conversación:', error);
            alert('Error creando conversación: ' + error.message);
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

    // MOSTRAR ERROR
    function showError(message) {
        elements.conversationsList.innerHTML = `
            <div class="p-4 text-center text-red-500">
                <i class="fas fa-exclamation-triangle text-4xl mb-2"></i>
                <p>${message}</p>
            </div>
        `;
    }

    // FORMATO DE TIEMPO
    function formatTime(timestamp) {
        if (!timestamp) return '';
        
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) { // Menos de 1 minuto
            return 'Ahora';
        } else if (diff < 3600000) { // Menos de 1 hora
            return `${Math.floor(diff / 60000)}m`;
        } else if (diff < 86400000) { // Menos de 1 día
            return `${Math.floor(diff / 3600000)}h`;
        } else {
            return date.toLocaleDateString();
        }
    }

    // ===== WEBSOCKET =====

    // INICIALIZAR WEBSOCKET
    function initWebSocket() {
        try {
            console.log('🔌 Conectando WebSocket...');
            
            const wsUrl = '<?= function_exists("getWebSocketUrl") ? getWebSocketUrl() : "ws://localhost:8080" ?>';
            console.log('🔌 URL del WebSocket:', wsUrl);
            
            websocket = new WebSocket(wsUrl);
            
            websocket.onopen = function(event) {
                console.log('✅ WebSocket conectado');
                isWebSocketConnected = true;
                if (elements.wsStatus) {
                    elements.wsStatus.className = 'absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white';
                }
                
                // Autenticar usuario
                const userId = <?= $_SESSION['user_id'] ?? 0 ?>;
                const userRole = '<?= $_SESSION['role'] ?? 'cliente' ?>';
                
                if (userId) {
                    websocket.send(JSON.stringify({
                        type: 'auth',
                        user_id: userId,
                        user_role: userRole
                    }));
                }
            };
            
            websocket.onmessage = function(event) {
                console.log('📨 WebSocket mensaje recibido:', event.data);
                
                try {
                    const data = JSON.parse(event.data);
                    handleWebSocketMessage(data);
                } catch (error) {
                    console.error('❌ Error parseando mensaje WebSocket:', error);
                }
            };
            
            websocket.onclose = function(event) {
                console.log('🔌 WebSocket desconectado');
                isWebSocketConnected = false;
                if (elements.wsStatus) {
                    elements.wsStatus.className = 'absolute -bottom-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white';
                }
                
                // Reintentar conexión después de 5 segundos
                setTimeout(() => {
                    if (!isWebSocketConnected) {
                        initWebSocket();
                    }
                }, 5000);
            };
            
            websocket.onerror = function(error) {
                console.error('❌ Error en WebSocket:', error);
                isWebSocketConnected = false;
                if (elements.wsStatus) {
                    elements.wsStatus.className = 'absolute -bottom-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white';
                }
            };
            
        } catch (error) {
            console.error('❌ Error inicializando WebSocket:', error);
        }
    }

    // MANEJAR MENSAJES WEBSOCKET
    function handleWebSocketMessage(data) {
        console.log('📨 Procesando mensaje WebSocket:', data);
        
        switch (data.type) {
            case 'auth':
                if (data.status === 'success') {
                    console.log('✅ Usuario autenticado en WebSocket');
                } else {
                    console.error('❌ Error de autenticación WebSocket:', data.message);
                }
                break;
                
            case 'direct_message':
                handleIncomingMessage(data);
                break;
                
            default:
                console.log('❓ Tipo de mensaje WebSocket desconocido:', data.type);
        }
    }

    // MANEJAR MENSAJE ENTRANTE
    function handleIncomingMessage(data) {
        // Solo procesar si es para la conversación actual
        if (currentChat && data.conversation_id == currentChat.conversacion_id) {
            // Agregar mensaje a la interfaz
            addMessageToUI(data.message, false, data.timestamp);
            
            // Actualizar último mensaje en la lista
            updateLastMessage(data.conversation_id, data.message);
        }
        
        // Actualizar conversaciones para mostrar nuevo mensaje
        loadConversations();
    }

    // ===== EVENT LISTENERS =====

    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 DOM cargado, configurando chat principal...');
        
        // Verificar elementos
        console.log('🔍 Verificando elementos del DOM...');
        Object.keys(elements).forEach(key => {
            console.log(`${key}:`, elements[key]);
        });
        
        // BOTÓN ENVIAR
        elements.sendMessage.addEventListener('click', function(e) {
            console.log('🖱️ Botón enviar clickeado');
            e.preventDefault();
            console.log('🚀 Llamando a sendMessage()...');
            sendMessage();
        });
        
        // INPUT ENTER
        elements.messageInput.addEventListener('keypress', function(e) {
            console.log('⌨️ Tecla presionada:', e.key);
            if (e.key === 'Enter' && !e.shiftKey) {
                console.log('🖱️ Enter presionado (sin Shift)');
                e.preventDefault();
                sendMessage();
            }
        });
        
        // BOTÓN NUEVO CHAT
        elements.newChatBtn.addEventListener('click', function() {
            console.log('🖱️ Botón nuevo chat clickeado');
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
        
        console.log('🚀 Event listeners configurados');
        
        // Cargar conversaciones iniciales
        loadConversations();
        
        // Inicializar WebSocket
        initWebSocket();
    });

    console.log('🚀 CHAT PRINCIPAL CARGADO');
</script> 