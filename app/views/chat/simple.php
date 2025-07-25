<?php
$pageTitle = 'Chat Simple - ' . APP_NAME;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f8f9fa',
                            100: '#e9ecef',
                            200: '#dee2e6',
                            300: '#ced4da',
                            400: '#adb5bd',
                            500: '#6c757d',
                            600: '#495057',
                            700: '#343a40',
                            800: '#1D3557',
                            900: '#152a47',
                        },
                        verde: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#2A9D8F',
                            600: '#238c7f',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --color-azul-marino: #1D3557;
            --color-dorado-suave: #F4A261;
            --color-verde-esmeralda: #2A9D8F;
            --color-gris-claro: #F8F9FA;
            --color-gris-oscuro: #495057;
            --text-primary: #1D3557;
            --bg-primary: #F8F9FA;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header con Navbar Mejorado -->
        <div class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo y Título -->
                    <div class="flex items-center space-x-4">
                        <a href="/" class="flex items-center space-x-2 group">
                            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center group-hover:bg-primary-700 transition-colors" style="background-color: #1D3557;">
                                <i class="fas fa-home text-white text-sm"></i>
                            </div>
                            <span class="text-lg font-bold text-gray-900 group-hover:text-primary-600 transition-colors" style="color: #1D3557;"><?= APP_NAME ?></span>
                        </a>
                        <div class="h-6 w-px bg-gray-300"></div>
                        <h1 class="text-lg font-semibold text-gray-900">Chat</h1>
                    </div>
                    
                    <!-- Información del Usuario y Estado -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600" id="user-display">
                                <?php 
                                $nombreCompleto = $user_nombre . ' ' . $user_apellido;
                                echo htmlspecialchars(trim($nombreCompleto) ?: 'Usuario');
                                ?>
                            </span>
                            <span id="ws-status" class="w-3 h-3 bg-red-500 rounded-full border-2 border-white"></span>
                        </div>
                        <a href="/" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors" style="color: var(--text-primary);">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto h-[calc(100vh-80px)] flex">
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
        
        <!-- Footer Simple -->
        <footer class="bg-white border-t border-gray-200 py-4">
            <div class="max-w-6xl mx-auto px-4 text-center">
                <p class="text-sm text-gray-600">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. Chat en tiempo real.
                </p>
            </div>
        </footer>
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
        // ===== CHAT SIMPLE DESDE CERO =====
        console.log('🚀 INICIANDO CHAT SIMPLE DESDE CERO');
        

        console.log('🔍 Variables de sesión disponibles:');
        console.log('user_id:', <?= $user_id ?? 'null' ?>);
        console.log('user_nombre:', '<?= $user_nombre ?? 'null' ?>');
        console.log('user_apellido:', '<?= $user_apellido ?? 'null' ?>');
        console.log('user_rol:', '<?= $user_role ?? 'null' ?>');
        console.log('user_email:', '<?= $user_email ?? 'null' ?>');

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
            
            const content = elements.messageInput.value.trim();
            
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
            
            // Intentar enviar por WebSocket primero
            if (isWebSocketConnected && websocket) {
                console.log('🔌 Enviando por WebSocket...');
                
                const messageData = {
                    type: 'direct_message',
                    conversation_id: currentChat.conversacion_id,
                    user_id: <?= $user_id ?? 0 ?>,
                    message: content
                };
                
                websocket.send(JSON.stringify(messageData));
                
                // Limpiar input inmediatamente
                elements.messageInput.value = '';
                
                // Agregar mensaje a la interfaz (optimistic update)
                addMessageToUI(content, true);
                
                // Actualizar último mensaje en la lista
                updateLastMessage(currentChat.conversacion_id, content);
                
                return;
            }
            
            // Fallback a HTTP si WebSocket no está disponible
            console.log('🌐 WebSocket no disponible, usando HTTP...');
            
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
        currentChat.current_user_id = <?= $user_id ?? 0 ?>;
            
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
                const isOwnMessage = msg.remitente_id == <?= $user_id ?? 0 ?>;
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
            console.log('🔍 Renderizando resultados de búsqueda:', users);
            
            if (!users || users.length === 0) {
                elements.searchResults.innerHTML = `
                    <div class="text-center text-gray-500 py-4">
                        <p>No se encontraron usuarios</p>
                    </div>
                `;
                return;
            }
            
            elements.searchResults.innerHTML = users.map(user => {
                const nombre = user.nombre || 'Sin nombre';
                const apellido = user.apellido || 'Sin apellido';
                const email = user.email || 'Sin email';
                const nombreCompleto = `${nombre} ${apellido}`.trim();
                
                console.log('👤 Usuario encontrado:', { id: user.id, nombre: nombreCompleto, email });
                
                return `
                    <div class="user-item p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors" 
                         data-user-id="${user.id}" data-user-name="${nombreCompleto}">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">${nombreCompleto}</h4>
                                <p class="text-sm text-gray-500">${email}</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
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

        // SELECCIONAR AGENTE POR ID
        async function selectAgentById(agentId) {
            try {
                console.log('🎯 Seleccionando agente por ID:', agentId);
                
                // Primero verificar si ya existe una conversación con este agente
                const existingConversation = conversations.find(conv => 
                    conv.agente_id == agentId || conv.cliente_id == agentId
                );
                
                if (existingConversation) {
                    console.log('✅ Conversación existente encontrada, seleccionando...');
                    selectConversation(existingConversation);
                    return;
                }
                
                // Si no existe, buscar el agente y crear conversación
                console.log('🔍 Buscando información del agente...');
                // Usar una búsqueda más específica para encontrar el agente por ID
                const response = await fetch(`/chat/search-users?q=id:${agentId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success && data.users && data.users.length > 0) {
                    const agent = data.users.find(user => user.id == agentId);
                    if (agent) {
                        console.log('✅ Agente encontrado:', agent);
                        await createConversation(agent.id, `${agent.nombre} ${agent.apellido}`);
                    } else {
                        console.error('❌ Agente no encontrado en los resultados');
                    }
                } else {
                    console.error('❌ No se encontró información del agente');
                }
            } catch (error) {
                console.error('❌ Error seleccionando agente:', error);
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
                
                websocket = new WebSocket('<?= function_exists("getWebSocketUrl") ? getWebSocketUrl() : "ws://localhost:8080" ?>');
                
                websocket.onopen = function(event) {
                    console.log('✅ WebSocket conectado');
                    isWebSocketConnected = true;
                    elements.wsStatus.className = 'w-3 h-3 bg-green-500 rounded-full';
                    
                                    // Autenticar usuario
                const userId = <?= $user_id ?? 0 ?>;
                const userRole = '<?= $user_role ?? 'cliente' ?>';
                    
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
                    elements.wsStatus.className = 'w-3 h-3 bg-red-500 rounded-full';
                    
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
                    elements.wsStatus.className = 'w-3 h-3 bg-red-500 rounded-full';
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
            console.log('📨 Mensaje entrante recibido:', data);
            
            // Solo procesar si es para la conversación actual
            if (currentChat && data.conversation_id == currentChat.conversacion_id) {
                console.log('✅ Mensaje para conversación actual, agregando a UI');
                
                // Verificar que no sea nuestro propio mensaje
                const isOwnMessage = data.user_id == <?= $user_id ?? 0 ?>;
                if (!isOwnMessage) {
                    // Agregar mensaje a la interfaz
                    addMessageToUI(data.message, false, data.timestamp);
                    
                    // Actualizar último mensaje en la lista
                    updateLastMessage(data.conversation_id, data.message);
                }
            } else {
                console.log('📝 Mensaje para otra conversación, actualizando lista');
            }
            
            // Actualizar conversaciones para mostrar nuevo mensaje
            loadConversations();
        }

        // ===== EVENT LISTENERS =====

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 DOM cargado, configurando chat simple...');
            
            // Verificar elementos
            console.log('🔍 Verificando elementos del DOM...');
            Object.keys(elements).forEach(key => {
                console.log(`${key}:`, elements[key]);
            });
            
            // Preseleccionar agente si se pasa como parámetro
            const urlParams = new URLSearchParams(window.location.search);
            const selectedAgentId = urlParams.get('agent');
            const propertyId = urlParams.get('property');
            
            if (selectedAgentId) {
                console.log('🎯 Agente preseleccionado:', selectedAgentId);
                // Buscar el agente y crear conversación automáticamente
                setTimeout(() => {
                    selectAgentById(selectedAgentId);
                }, 1000); // Esperar a que se carguen las conversaciones
            }
            
            // BOTÓN ENVIAR
            elements.sendMessage.addEventListener('click', function(e) {
                console.log('🖱️ Botón enviar clickeado');
                e.preventDefault();
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

        console.log('🚀 CHAT SIMPLE CARGADO');
    </script>
</body>
</html> 