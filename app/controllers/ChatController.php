<?php
/**
 * Controlador de Chat - PropEasy
 * Maneja todas las operaciones relacionadas con el chat
 */

require_once APP_PATH . '/models/Chat.php';
require_once APP_PATH . '/models/User.php';

class ChatController {
    private $chatModel;
    private $userModel;

    public function __construct() {
        $this->chatModel = new Chat();
        $this->userModel = new User();
    }

    /**
     * Página principal del chat - Redirige a chat simple
     */
    public function index() {
        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Redirigir al chat simple que funciona
        header('Location: /chat/simple');
        exit;
    }

    /**
     * Chat simple desde cero
     */
    public function simple() {
        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Asegurar que las variables de sesión estén disponibles
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';
        $user_nombre = $_SESSION['user_nombre'] ?? $_SESSION['nombre'] ?? 'Usuario';
        $user_apellido = $_SESSION['user_apellido'] ?? $_SESSION['apellido'] ?? '';
        $user_email = $_SESSION['user_email'] ?? $_SESSION['email'] ?? '';

        // Si no hay nombre, intentar obtenerlo de la base de datos
        if (empty($user_nombre) || $user_nombre === 'Usuario') {
            $user = $this->userModel->getById($user_id);
            if ($user) {
                $user_nombre = $user['nombre'] ?? 'Usuario';
                $user_apellido = $user['apellido'] ?? '';
                $user_email = $user['email'] ?? '';
                
                // Actualizar sesión
                $_SESSION['user_nombre'] = $user_nombre;
                $_SESSION['user_apellido'] = $user_apellido;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['user_rol'] = $user['rol'] ?? $user_role;
            }
        }

        // Cargar vista del chat simple
        include APP_PATH . '/views/chat/simple.php';
    }

    /**
     * Mostrar conversación específica
     */
    public function show($solicitud_id) {
        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';
        $user_name = $_SESSION['nombre'] ?? 'Usuario';

        // Verificar que el usuario tenga acceso a esta conversación
        $conversacion = $this->chatModel->getConversacion($solicitud_id, $user_id);
        
        if (!$conversacion) {
            http_response_code(404);
            include APP_PATH . '/views/errors/404.php';
            return;
        }

        // Obtener mensajes
        $mensajes = $this->chatModel->getMensajes($solicitud_id);
        
        // Marcar como leído
        $this->chatModel->marcarComoLeidos($solicitud_id, $user_id, $user_role);

        // Cargar vista
        $this->loadView('chat/show', [
            'conversacion' => $conversacion,
            'mensajes' => $mensajes,
            'user_id' => $user_id,
            'user_role' => $user_role,
            'user_name' => $user_name,
            'solicitud_id' => $solicitud_id
        ]);
    }

    /**
     * API para enviar mensaje
     */
    public function sendMessage($solicitud_id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $mensaje = $_POST['mensaje'] ?? '';

        if (empty($mensaje)) {
            http_response_code(400);
            echo json_encode(['error' => 'Mensaje requerido']);
            return;
        }

        // Verificar acceso a la conversación
        $conversacion = $this->chatModel->getConversacion($solicitud_id, $user_id);
        if (!$conversacion) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        // Enviar mensaje
        $result = $this->chatModel->enviarMensaje($solicitud_id, $user_id, $_SESSION['role'] ?? 'cliente', $mensaje);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Mensaje enviado correctamente',
                'mensaje_id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al enviar mensaje']);
        }
    }

    /**
     * API para obtener mensajes
     */
    public function getMessages($solicitud_id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];

        // Verificar acceso a la conversación
        $conversacion = $this->chatModel->getConversacion($solicitud_id, $user_id);
        if (!$conversacion) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        $mensajes = $this->chatModel->getMensajes($solicitud_id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'messages' => $mensajes
        ]);
    }

    /**
     * API para obtener mensajes no leídos
     */
    public function getUnreadMessages() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';

        $mensajesNoLeidos = $this->chatModel->getMensajesNoLeidos($user_id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'unread_messages' => $mensajesNoLeidos
        ]);
    }

    /**
     * API para marcar como leído
     */
    public function markAsRead($solicitud_id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];

        // Verificar acceso a la conversación
        $conversacion = $this->chatModel->getConversacion($solicitud_id, $user_id);
        if (!$conversacion) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        $result = $this->chatModel->marcarComoLeidos($solicitud_id, $user_id, $_SESSION['role'] ?? 'cliente');
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Marcado como leído' : 'Error al marcar como leído'
        ]);
    }

    /**
     * API para obtener conversaciones (para el chat integrado)
     */
    public function conversations() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';

        $conversaciones = $this->chatModel->getConversaciones($user_id, $user_role);
        
        // Formatear para el chat integrado
        $formatted_conversations = [];
        foreach ($conversaciones as $conv) {
            // Determinar el ID del otro usuario basado en el rol del usuario actual
            $otro_usuario_id = ($user_role === 'cliente') ? $conv['agente_id'] : $conv['cliente_id'];
            
            $formatted_conversations[] = [
                'id' => $conv['solicitud_id'],
                'name' => $conv['nombre_otro_usuario'],
                'online' => $this->isUserOnline($otro_usuario_id),
                'unread' => $conv['mensajes_no_leidos'] ?? 0,
                'last_message' => $conv['ultimo_mensaje'] ?? '',
                'last_message_time' => $conv['fecha_ultimo_mensaje'] ?? ''
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'conversations' => $formatted_conversations
        ]);
    }

    /**
     * API para obtener mensajes de una conversación específica
     */
    public function messages($conversation_id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'] ?? 'cliente';

        // Verificar acceso a la conversación
        $conversacion = $this->chatModel->getConversacion($conversation_id, $user_id);
        if (!$conversacion) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        $mensajes = $this->chatModel->getMensajes($conversation_id);
        
        // Marcar como leído
        $this->chatModel->marcarComoLeidos($conversation_id, $user_id, $user_role);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'messages' => $mensajes
        ]);
    }

    /**
     * API para enviar mensaje (versión JSON)
     */
    public function sendMessageJson() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        // Obtener datos JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $conversation_id = $input['conversation_id'] ?? null;
        $message = $input['message'] ?? '';

        if (!$conversation_id || empty($message)) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos requeridos']);
            return;
        }

        $user_id = $_SESSION['user_id'];

        // Verificar acceso a la conversación
        $conversacion = $this->chatModel->getConversacion($conversation_id, $user_id);
        if (!$conversacion) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        // Enviar mensaje
        $result = $this->chatModel->enviarMensaje($conversation_id, $user_id, $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente', $message);
        
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Mensaje enviado correctamente',
                'message_id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al enviar mensaje']);
        }
    }

    /**
     * API para obtener mensajes no leídos (versión simplificada)
     */
    public function unreadMessages() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';

        $mensajesNoLeidos = $this->chatModel->getMensajesNoLeidos($user_id);
        $total = count($mensajesNoLeidos);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'total' => $total,
            'unread_messages' => $mensajesNoLeidos
        ]);
    }

    /**
     * Verificar si un usuario está online
     */
    private function isUserOnline($user_id) {
        // Implementación simple: considerar online si ha estado activo en los últimos 5 minutos
        $last_activity = $this->userModel->getLastActivity($user_id);
        if (!$last_activity) return false;
        
        $five_minutes_ago = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        return $last_activity > $five_minutes_ago;
    }

    /**
     * API para obtener estadísticas
     */
    public function getStats() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';

        $stats = $this->getChatStats($user_id, $user_role);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * API para buscar conversaciones
     */
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';
        $query = $_GET['q'] ?? '';

        if (empty($query)) {
            http_response_code(400);
            echo json_encode(['error' => 'Término de búsqueda requerido']);
            return;
        }

        $resultados = $this->chatModel->buscarConversaciones($user_id, $user_role, $query);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'results' => $resultados
        ]);
    }

    /**
     * API para buscar usuarios para nueva conversación
     */
    public function searchUsers() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';
        $query = $_GET['q'] ?? '';

        if (empty($query) || strlen($query) < 2) {
            http_response_code(400);
            echo json_encode(['error' => 'Término de búsqueda debe tener al menos 2 caracteres']);
            return;
        }

        // Buscar usuarios según el rol del usuario actual
        $usuarios = [];
        if ($user_role === 'agente') {
            // Los agentes pueden buscar clientes Y otros agentes
            $usuarios = $this->userModel->searchUsers($query, null, $user_id);
            // Filtrar para excluir al usuario actual y solo mostrar clientes y otros agentes
            $usuarios = array_filter($usuarios, function($user) use ($user_id) {
                return $user['id'] != $user_id && ($user['rol'] === 'cliente' || $user['rol'] === 'agente');
            });
        } elseif ($user_role === 'cliente') {
            // Los clientes pueden buscar agentes
            $usuarios = $this->userModel->searchUsers($query, 'agente', $user_id);
        } elseif ($user_role === 'admin') {
            // Los admins pueden buscar cualquier usuario
            $usuarios = $this->userModel->searchUsers($query, null, $user_id);
        }

        // Formatear resultados
        $formatted_users = [];
        foreach ($usuarios as $user) {
            // Manejar nombres nulos o vacíos
            $nombre = $user['nombre'] ?? '';
            $apellido = $user['apellido'] ?? '';
            $nombreCompleto = trim($nombre . ' ' . $apellido);
            
            // Si no hay nombre, usar email como fallback
            if (empty($nombreCompleto)) {
                $nombreCompleto = $user['email'] ?? 'Usuario sin nombre';
            }
            
            $formatted_users[] = [
                'id' => $user['id'],
                'nombre' => $nombre,
                'apellido' => $apellido,
                'name' => $nombreCompleto,
                'email' => $user['email'],
                'online' => $this->isUserOnline($user['id']),
                'role' => $user['rol']
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'users' => $formatted_users
        ]);
    }

    /**
     * API para crear nueva conversación
     */
    public function createConversation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        // Obtener datos JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $other_user_id = $input['user_id'] ?? null;
        $initial_message = $input['initial_message'] ?? '';

        if (!$other_user_id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de usuario requerido']);
            return;
        }

        $current_user_id = $_SESSION['user_id'];
        $current_user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';

        // Verificar que el usuario existe y tiene el rol correcto
        $other_user = $this->userModel->getById($other_user_id);
        if (!$other_user) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuario no encontrado']);
            return;
        }

        // Verificar que no estamos creando una conversación con nosotros mismos
        if ($current_user_id == $other_user_id) {
            http_response_code(400);
            echo json_encode(['error' => 'No puedes crear una conversación contigo mismo']);
            return;
        }

        // Verificar permisos según roles
        if ($current_user_role === 'agente' && $other_user['rol'] !== 'cliente') {
            http_response_code(403);
            echo json_encode(['error' => 'Los agentes solo pueden conversar con clientes']);
            return;
        } elseif ($current_user_role === 'cliente' && $other_user['rol'] !== 'agente') {
            http_response_code(403);
            echo json_encode(['error' => 'Los clientes solo pueden conversar con agentes']);
            return;
        }

        // Crear o obtener conversación existente
        $solicitud_id = $this->chatModel->crearObtenerSolicitud($current_user_id, $other_user_id);
        
        if ($solicitud_id) {
            // Si hay mensaje inicial, enviarlo
            if (!empty($initial_message)) {
                $this->chatModel->enviarMensaje($solicitud_id, $current_user_id, $current_user_role, $initial_message);
            }
            
            // Obtener información de la conversación creada
            $conversacion = $this->chatModel->getConversacion($solicitud_id, $current_user_id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Conversación creada exitosamente',
                'conversation_id' => $solicitud_id,
                'conversation' => $conversacion
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear la conversación']);
        }
    }

    /**
     * Iniciar conversación con nuevo cliente
     */
    public function iniciarConNuevoCliente($cliente_id) {
        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agente') {
            header('Location: /login');
            exit;
        }

        $agente_id = $_SESSION['user_id'];

        // Verificar que el cliente existe
        $cliente = $this->userModel->getById($cliente_id);
        if (!$cliente || $cliente['rol'] !== 'cliente') {
            http_response_code(404);
            include APP_PATH . '/views/errors/404.php';
            return;
        }

        // Crear o obtener solicitud existente
        $solicitud_id = $this->chatModel->crearObtenerSolicitud($agente_id, $cliente_id);
        
        if ($solicitud_id) {
            header("Location: /chat/{$solicitud_id}");
        } else {
            http_response_code(500);
            include APP_PATH . '/views/errors/500.php';
        }
    }

    /**
     * Obtener estadísticas del chat
     */
    private function getChatStats($user_id, $user_role) {
        $stats = [
            'total_conversaciones' => 0,
            'mensajes_no_leidos' => 0,
            'conversaciones_activas' => 0,
            'ultima_actividad' => null
        ];

        try {
            $conversaciones = $this->chatModel->getConversaciones($user_id, $user_role);
            $stats['total_conversaciones'] = count($conversaciones);
            
            $mensajesNoLeidos = 0;
            $conversacionesActivas = 0;
            $ultimaActividad = null;

            foreach ($conversaciones as $conv) {
                if ($conv['mensajes_no_leidos'] > 0) {
                    $mensajesNoLeidos += $conv['mensajes_no_leidos'];
                }
                
                if (strtotime($conv['ultima_actividad']) > strtotime('-7 days')) {
                    $conversacionesActivas++;
                }
                
                if (!$ultimaActividad || strtotime($conv['ultima_actividad']) > strtotime($ultimaActividad)) {
                    $ultimaActividad = $conv['ultima_actividad'];
                }
            }

            $stats['mensajes_no_leidos'] = $mensajesNoLeidos;
            $stats['conversaciones_activas'] = $conversacionesActivas;
            $stats['ultima_actividad'] = $ultimaActividad;

        } catch (Exception $e) {
            error_log("Error obteniendo estadísticas del chat: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Cargar vista
     */
    private function loadView($view, $data = []) {
        extract($data);
        $viewPath = APP_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            // Capturar el contenido de la vista
            ob_start();
            require_once $viewPath;
            $content = ob_get_clean();
            
            // Incluir el layout principal con el contenido
            require_once APP_PATH . '/views/layouts/main.php';
        } else {
            throw new Exception("Vista no encontrada: {$viewPath}");
        }
    }

    /**
     * API para enviar mensaje directo (sin solicitud de compra)
     */
    public function sendDirectMessage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        // Obtener datos JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $conversation_id = $input['conversation_id'] ?? null;
        $message = $input['message'] ?? '';

        if (!$conversation_id || empty($message)) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos requeridos']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';

        // Verificar acceso a la conversación directa
        if (!$this->chatModel->tieneAccesoConversacionDirecta($conversation_id, $user_id)) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        // Enviar mensaje directo
        $result = $this->chatModel->enviarMensajeDirecto($conversation_id, $user_id, $user_role, $message);
        
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Mensaje enviado correctamente',
                'message_id' => $result
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al enviar mensaje']);
        }
    }

    /**
     * API para obtener conversaciones directas
     */
    public function directConversations() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';

        $conversaciones = $this->chatModel->getConversacionesDirectas($user_id, $user_role);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'conversations' => $conversaciones
        ]);
    }

    /**
     * API para obtener mensajes de conversación directa
     */
    public function directMessages($conversation_id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $user_id = $_SESSION['user_id'];

        // Verificar acceso a la conversación directa
        if (!$this->chatModel->tieneAccesoConversacionDirecta($conversation_id, $user_id)) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        $mensajes = $this->chatModel->getMensajesDirectos($conversation_id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'messages' => $mensajes
        ]);
    }

    /**
     * API para crear conversación directa
     */
    public function createDirectConversation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        // Obtener datos JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $user_id = $input['user_id'] ?? null;

        if (!$user_id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de usuario requerido']);
            return;
        }

        $current_user_id = $_SESSION['user_id'];

        // Crear conversación directa
        $result = $this->chatModel->crearObtenerConversacionDirecta($current_user_id, $user_id);
        
        header('Content-Type: application/json');
        if ($result) {
            // Obtener información de la conversación creada
            $conversacion = $this->chatModel->getConversacionDirecta($result, $current_user_id);
            echo json_encode([
                'success' => true,
                'conversation' => $conversacion,
                'message' => 'Conversación creada correctamente'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear conversación']);
        }
    }

    /**
     * API para obtener usuarios disponibles para chat directo
     */
    public function usersForDirectChat() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Verificar sesión de forma segura
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $current_user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_rol'] ?? $_SESSION['role'] ?? 'cliente';
        $search_query = $_GET['search'] ?? '';

        // Obtener usuarios disponibles (excluyendo al usuario actual)
        $users = $this->userModel->getUsersForDirectChat($current_user_id, $user_role, $search_query);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'users' => $users
        ]);
    }
} 