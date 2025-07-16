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
        
        /* Estilos para botones de favoritos */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .btn-outline-danger {
            color: #dc2626;
            border-color: #dc2626;
            background-color: transparent;
        }
        
        .btn-outline-danger:hover {
            color: white;
            background-color: #dc2626;
        }
        
        .btn-danger {
            color: white;
            border-color: #dc2626;
            background-color: #dc2626;
        }
        
        .btn-danger:hover {
            background-color: #b91c1c;
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Animaciones para favoritos */
        .animate__animated {
            animation-duration: 1s;
            animation-fill-mode: both;
        }
        
        .animate__heartBeat {
            animation-name: heartBeat;
        }
        
        .animate__fadeOut {
            animation-name: fadeOut;
        }
        
        .animate__pulse {
            animation-name: pulse;
        }
        
        @keyframes heartBeat {
            0% { transform: scale(1); }
            14% { transform: scale(1.3); }
            28% { transform: scale(1); }
            42% { transform: scale(1.3); }
            70% { transform: scale(1); }
        }
        
        @keyframes fadeOut {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Contador de favoritos */
        .favorite-counter {
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 1.25rem;
            height: 1.25rem;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col <?= isAuthenticated() ? 'user-authenticated' : '' ?>">
    <!-- Header con Navbar del Cliente -->
    <?php include APP_PATH . '/views/components/navbar.php'; ?>

    <!-- Contenido principal -->
    <main class="flex-1 pt-20">
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
        
        // Cargar sistema de favoritos
        <?php if (isAuthenticated()): ?>
        // Incluir script de favoritos
        const favoritesScript = document.createElement('script');
        favoritesScript.src = '/js/favorites.js';
        favoritesScript.onload = function() {
            console.log('Sistema de favoritos cargado correctamente');
        };
        favoritesScript.onerror = function() {
            console.error('Error al cargar el sistema de favoritos');
        };
        document.head.appendChild(favoritesScript);
        <?php endif; ?>

    </script>
</body>
</html> 