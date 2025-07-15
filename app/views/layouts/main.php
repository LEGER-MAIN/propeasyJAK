<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        .slide-in { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        .chat-tab.active {
            background-color: #3b82f6;
            color: white;
        }
        
        .chat-message {
            max-width: 80%;
            word-wrap: break-word;
        }
        
        .chat-message.sent {
            background-color: #3b82f6;
            color: white;
            border-radius: 18px 18px 4px 18px;
        }
        
        .chat-message.received {
            background-color: #f3f4f6;
            color: #374151;
            border-radius: 18px 18px 18px 4px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-home text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900"><?= APP_NAME ?></span>
                    </a>
                </div>
                
                <!-- Navegación -->
                <nav class="hidden md:flex space-x-8">
                    <a href="/" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Inicio
                    </a>
                    <a href="/properties" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Propiedades
                    </a>
                    <?php if (isAuthenticated()): ?>
                        <?php if (hasRole(ROLE_AGENTE)): ?>
                            <a href="/properties/agent/list" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Mis Propiedades
                            </a>
                            <a href="/properties/pending-validation" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Pendientes
                            </a>
                        <?php endif; ?>
                        <a href="/dashboard" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Dashboard
                        </a>
                        <?php if (hasRole(ROLE_CLIENTE)): ?>
                            <a href="/buscar-agentes" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-search"></i> Buscar Agentes
                            </a>
                        <?php endif; ?>
                        <?php if (hasRole(ROLE_AGENTE)): ?>
                            <a href="/buscar-clientes" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="fas fa-search"></i> Buscar Clientes
                            </a>
                        <?php endif; ?>
                        <a href="/chat" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="fas fa-comments"></i> Chat Completo
                        </a>
                        <a href="/favorites" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors relative">
                            <i class="fas fa-heart"></i> Favoritos
                            <span id="favorite-count" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                        </a>
                    <?php endif; ?>
                </nav>
                
                <!-- Menú de usuario y chat -->
                <div class="flex items-center space-x-4">
                    <?php if (isAuthenticated()): ?>
                        <!-- Chat Button -->
                        <div class="relative">
                            <button id="chat-toggle" class="relative p-2 text-gray-700 hover:text-primary-600 transition-colors">
                                <i class="fas fa-comments text-lg"></i>
                                <span id="chat-notification" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                            </button>
                            
                            <!-- Chat Panel -->
                            <div id="chat-panel" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                <!-- Chat Header -->
                                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-comments text-primary-600"></i>
                                        <h3 class="font-semibold text-gray-900">Chat</h3>
                                    </div>
                                    <button id="chat-close" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <!-- Chat Tabs -->
                                <div id="chat-tabs" class="flex border-b border-gray-200">
                                    <!-- Tabs se cargarán dinámicamente -->
                                </div>
                                
                                <!-- Chat Content -->
                                <div id="chat-content" class="h-96 flex flex-col">
                                    <!-- Messages Area -->
                                    <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3">
                                        <div class="text-center text-gray-500 text-sm">
                                            <i class="fas fa-comments text-2xl mb-2 block"></i>
                                            <p>Selecciona una conversación para comenzar</p>
                                            <p class="text-xs mt-1">O ve al chat completo para iniciar nuevas conversaciones</p>
                                            <a href="/chat" class="inline-block mt-2 px-3 py-1 bg-primary-600 text-white rounded text-xs hover:bg-primary-700 transition-colors">
                                                Ir al Chat Completo
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Message Input -->
                                    <div class="p-4 border-t border-gray-200">
                                        <div class="flex space-x-2">
                                            <input type="text" id="chat-input" placeholder="Escribe tu mensaje..." 
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                            <button id="chat-send" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Menú de usuario -->
                        <div class="relative">
                            <button id="user-menu-button" class="w-9 h-9 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold text-lg hover:bg-primary-200 transition-colors">
                                <?= strtoupper(substr($_SESSION['user_nombre'] ?? 'U', 0, 1)) ?>
                            </button>
                            <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50">
                                <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                    <?= htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario') ?>
                                </div>
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Mi Perfil</a>
                                <?php if (hasRole(ROLE_ADMIN)): ?>
                                    <a href="/admin/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Panel Admin</a>
                                <?php endif; ?>
                                <?php if (hasRole(ROLE_AGENTE)): ?>
                                    <a href="/agente/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Panel Agente</a>
                                <?php endif; ?>
                                <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Cerrar Sesión</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Botones de autenticación -->
                        <a href="/login" class="text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="/register" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Registrarse
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-1">
        <!-- Mensajes flash -->
        <?php $flashMessages = getFlashMessages(); ?>
        <?php if (!empty($flashMessages)): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <?php foreach ($flashMessages as $message): ?>
                    <div class="fade-in mb-4 p-4 rounded-md 
                        <?php if ($message['type'] === 'success'): ?>
                            bg-green-50 border border-green-200 text-green-800
                        <?php elseif ($message['type'] === 'info'): ?>
                            bg-blue-50 border border-blue-200 text-blue-800
                        <?php else: ?>
                            bg-red-50 border border-red-200 text-red-800
                        <?php endif; ?>">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <?php if ($message['type'] === 'success'): ?>
                                    <i class="fas fa-check-circle text-green-400"></i>
                                <?php elseif ($message['type'] === 'info'): ?>
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                <?php else: ?>
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium">
                                    <?= $message['message'] ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Contenido de la página -->
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php else: ?>
            <!-- El contenido se incluye directamente desde la vista -->
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-home text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold"><?= APP_NAME ?></span>
                    </div>
                    <p class="text-gray-300">
                        Plataforma líder en gestión inmobiliaria, conectando clientes y agentes.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Enlaces</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-300 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="/properties" class="text-gray-300 hover:text-white transition-colors">Propiedades</a></li>
                        <li><a href="/about" class="text-gray-300 hover:text-white transition-colors">Acerca de</a></li>
                        <li><a href="/contact" class="text-gray-300 hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                    <div class="space-y-2 text-gray-300">
                        <p><i class="fas fa-envelope mr-2"></i>info@propeasy.com</p>
                        <p><i class="fas fa-phone mr-2"></i>+1 809 555 0123</p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i>Santo Domingo, RD</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Variables globales del chat
        let currentChatId = null;
        let chatTabs = [];
        let chatMessages = {};
        
        // Menú de usuario
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenuDropdown = document.getElementById('user-menu-dropdown');
        
        if (userMenuButton && userMenuDropdown) {
            userMenuButton.addEventListener('click', function() {
                userMenuDropdown.classList.toggle('hidden');
            });
            
            // Cerrar menú al hacer clic fuera
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userMenuDropdown.contains(event.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        }
        
        <?php if (isAuthenticated()): ?>
        // Funcionalidad del chat
        const chatToggle = document.getElementById('chat-toggle');
        const chatPanel = document.getElementById('chat-panel');
        const chatClose = document.getElementById('chat-close');
        const chatTabsContainer = document.getElementById('chat-tabs');
        const chatMessagesContainer = document.getElementById('chat-messages');
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');
        
        // Toggle del chat
        if (chatToggle && chatPanel) {
            chatToggle.addEventListener('click', function() {
                chatPanel.classList.toggle('hidden');
                if (!chatPanel.classList.contains('hidden')) {
                    loadChatTabs();
                }
            });
            
            chatClose.addEventListener('click', function() {
                chatPanel.classList.add('hidden');
            });
            
            // Cerrar chat al hacer clic fuera
            document.addEventListener('click', function(event) {
                if (!chatToggle.contains(event.target) && !chatPanel.contains(event.target)) {
                    chatPanel.classList.add('hidden');
                }
            });
        }
        
        // Cargar pestañas del chat
        function loadChatTabs() {
            fetch('/chat/conversations')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chatTabs = data.conversations;
                        renderChatTabs();
                        if (chatTabs.length > 0 && !currentChatId) {
                            selectChatTab(chatTabs[0].id);
                        }
                    }
                })
                .catch(error => console.error('Error al cargar conversaciones:', error));
        }
        
        // Renderizar pestañas del chat
        function renderChatTabs() {
            chatTabsContainer.innerHTML = '';
            
            if (chatTabs.length === 0) {
                chatTabsContainer.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-comments text-2xl mb-2"></i>
                        <p class="text-sm">No hay conversaciones</p>
                        <a href="/chat" class="inline-block mt-2 px-3 py-1 bg-primary-600 text-white rounded text-xs hover:bg-primary-700 transition-colors">
                            Ir al Chat Completo
                        </a>
                    </div>
                `;
                return;
            }
            
            chatTabs.forEach(conversation => {
                const tab = document.createElement('div');
                tab.className = `chat-tab flex-1 px-3 py-2 text-center text-sm cursor-pointer transition-colors ${conversation.id === currentChatId ? 'active' : 'hover:bg-gray-100'}`;
                tab.innerHTML = `
                    <div class="flex items-center justify-center space-x-2">
                        <div class="w-2 h-2 rounded-full ${conversation.online ? 'bg-green-500' : 'bg-gray-400'}"></div>
                        <span class="truncate">${conversation.name}</span>
                        ${conversation.unread > 0 ? `<span class="bg-red-500 text-white text-xs rounded-full px-1">${conversation.unread}</span>` : ''}
                    </div>
                `;
                tab.addEventListener('click', () => selectChatTab(conversation.id));
                chatTabsContainer.appendChild(tab);
            });
        }
        
        // Seleccionar pestaña del chat
        function selectChatTab(chatId) {
            currentChatId = chatId;
            renderChatTabs();
            loadChatMessages(chatId);
        }
        
        // Cargar mensajes del chat
        function loadChatMessages(chatId) {
            fetch(`/chat/messages/${chatId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chatMessages[chatId] = data.messages;
                        renderChatMessages(chatId);
                    }
                })
                .catch(error => console.error('Error al cargar mensajes:', error));
        }
        
        // Renderizar mensajes del chat
        function renderChatMessages(chatId) {
            const messages = chatMessages[chatId] || [];
            chatMessagesContainer.innerHTML = '';
            
            if (messages.length === 0) {
                chatMessagesContainer.innerHTML = `
                    <div class="text-center text-gray-500 text-sm">
                        No hay mensajes en esta conversación
                    </div>
                `;
                return;
            }
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `flex ${message.sender_id == <?= $_SESSION['user_id'] ?? 0 ?> ? 'justify-end' : 'justify-start'}`;
                messageDiv.innerHTML = `
                    <div class="chat-message ${message.sender_id == <?= $_SESSION['user_id'] ?? 0 ?> ? 'sent' : 'received'} px-4 py-2">
                        <div class="text-sm">${message.message}</div>
                        <div class="text-xs opacity-75 mt-1">${new Date(message.created_at).toLocaleTimeString()}</div>
                    </div>
                `;
                chatMessagesContainer.appendChild(messageDiv);
            });
            
            // Scroll al final
            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
        }
        
        // Enviar mensaje
        function sendMessage() {
            if (!currentChatId || !chatInput.value.trim()) return;
            
            const message = chatInput.value.trim();
            chatInput.value = '';
            
            fetch('/chat/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    conversation_id: currentChatId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Agregar mensaje a la conversación actual
                    if (!chatMessages[currentChatId]) {
                        chatMessages[currentChatId] = [];
                    }
                    chatMessages[currentChatId].push({
                        id: data.message_id,
                        message: message,
                        sender_id: <?= $_SESSION['user_id'] ?? 0 ?>,
                        created_at: new Date().toISOString()
                    });
                    renderChatMessages(currentChatId);
                }
            })
            .catch(error => console.error('Error al enviar mensaje:', error));
        }
        
        // Event listeners para envío de mensajes
        if (chatSend) {
            chatSend.addEventListener('click', sendMessage);
        }
        
        if (chatInput) {
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        }
        
        // Actualizar notificaciones del chat
        function updateChatNotifications() {
            fetch('/chat/unread-messages')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const notification = document.getElementById('chat-notification');
                        if (data.total > 0) {
                            notification.textContent = data.total > 99 ? '99+' : data.total;
                            notification.classList.remove('hidden');
                        } else {
                            notification.classList.add('hidden');
                        }
                    }
                })
                .catch(error => console.error('Error al obtener notificaciones del chat:', error));
        }
        
        // Actualizar notificaciones cada 30 segundos
        setInterval(updateChatNotifications, 30000);
        
        // Actualizar al cargar la página
        updateChatNotifications();
        <?php endif; ?>
        
        <!-- Sistema de Favoritos -->
        <?php if (isAuthenticated()): ?>
        <script src="/js/favorites.js"></script>
        <?php endif; ?>
    </script>
</body>
</html> 