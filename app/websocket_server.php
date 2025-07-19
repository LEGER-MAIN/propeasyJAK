<?php
/**
 * Servidor WebSocket para Chat en Tiempo Real
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

// Definir constantes necesarias
define('APP_PATH', __DIR__);

// Cargar configuración
require_once __DIR__ . '/../config/config.php';

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/models/User.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatWebSocket implements MessageComponentInterface {
    protected $clients;
    protected $userConnections;
    protected $db;
    protected $userModel;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->userConnections = [];
        $this->db = new Database();
        $this->userModel = new User();
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nueva conexión! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if (!$data) {
            return;
        }

        $type = $data['type'] ?? '';
        
        switch ($type) {
            case 'auth':
                $this->handleAuth($from, $data);
                break;
            case 'message':
                $this->handleMessage($from, $data);
                break;
            case 'direct_message':
                $this->handleDirectMessage($from, $data);
                break;
            case 'typing':
                $this->handleTyping($from, $data);
                break;
            case 'read':
                $this->handleRead($from, $data);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        
        // Remover de conexiones de usuario
        foreach ($this->userConnections as $userId => $connection) {
            if ($connection === $conn) {
                unset($this->userConnections[$userId]);
                break;
            }
        }
        
        echo "Conexión {$conn->resourceId} se ha desconectado\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function handleAuth($conn, $data) {
        $userId = $data['user_id'] ?? null;
        
        if ($userId) {
            $this->userConnections[$userId] = $conn;
            
            // Actualizar estado online
            $this->userModel->updateLastActivity($userId);
            
            $conn->send(json_encode([
                'type' => 'auth',
                'status' => 'success',
                'user_id' => $userId
            ]));
            
            echo "Usuario {$userId} autenticado\n";
        }
    }

    protected function handleMessage($conn, $data) {
        $solicitudId = $data['solicitud_id'] ?? null;
        $userId = $data['user_id'] ?? null;
        $message = $data['message'] ?? '';
        $userRole = $data['user_role'] ?? 'cliente';
        
        if (!$solicitudId || !$userId || empty($message)) {
            return;
        }

        // Guardar mensaje en base de datos
        $sql = "INSERT INTO mensajes_chat (solicitud_id, remitente_id, remitente_rol, mensaje) VALUES (?, ?, ?, ?)";
        $messageId = $this->db->insert($sql, [$solicitudId, $userId, $userRole, $message]);
        
        if ($messageId) {
            // Obtener información del usuario
            $userInfo = $this->userModel->getById($userId);
            
            $messageData = [
                'type' => 'message',
                'solicitud_id' => $solicitudId,
                'message_id' => $messageId,
                'user_id' => $userId,
                'user_name' => $userInfo['nombre'] . ' ' . $userInfo['apellido'],
                'user_role' => $userRole,
                'message' => $message,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            // Enviar a todos los usuarios en la conversación
            $this->broadcastToConversation($solicitudId, $messageData);
        }
    }

    protected function handleDirectMessage($conn, $data) {
        $conversationId = $data['conversation_id'] ?? null;
        $userId = $data['user_id'] ?? null;
        $message = $data['message'] ?? '';
        
        if (!$conversationId || !$userId || empty($message)) {
            return;
        }

        // Guardar mensaje en base de datos
        $sql = "INSERT INTO mensajes_directos (conversacion_id, remitente_id, mensaje) VALUES (?, ?, ?)";
        $messageId = $this->db->insert($sql, [$conversationId, $userId, $message]);
        
        if ($messageId) {
            // Obtener información del usuario
            $userInfo = $this->userModel->getById($userId);
            
            $messageData = [
                'type' => 'direct_message',
                'conversation_id' => $conversationId,
                'message_id' => $messageId,
                'user_id' => $userId,
                'user_name' => ($userInfo['nombre'] ?? 'Sin nombre') . ' ' . ($userInfo['apellido'] ?? 'Sin apellido'),
                'message' => $message,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            // Enviar a todos los usuarios en la conversación directa
            $this->broadcastToDirectConversation($conversationId, $messageData);
        }
    }

    protected function handleTyping($conn, $data) {
        $solicitudId = $data['solicitud_id'] ?? null;
        $userId = $data['user_id'] ?? null;
        $isTyping = $data['typing'] ?? false;
        
        if (!$solicitudId || !$userId) {
            return;
        }
        
        $typingData = [
            'type' => 'typing',
            'solicitud_id' => $solicitudId,
            'user_id' => $userId,
            'typing' => $isTyping
        ];
        
        $this->broadcastToConversation($solicitudId, $typingData);
    }

    protected function handleRead($conn, $data) {
        $solicitudId = $data['solicitud_id'] ?? null;
        $userId = $data['user_id'] ?? null;
        
        if (!$solicitudId || !$userId) {
            return;
        }
        
        // Marcar mensajes como leídos
        $sql = "UPDATE mensajes_chat SET leido = 1 WHERE solicitud_id = ? AND remitente_id != ?";
        $this->db->update($sql, [$solicitudId, $userId]);
        
        $readData = [
            'type' => 'read',
            'solicitud_id' => $solicitudId,
            'user_id' => $userId
        ];
        
        $this->broadcastToConversation($solicitudId, $readData);
    }

    protected function broadcastToConversation($solicitudId, $data) {
        // Obtener usuarios de la conversación
        $sql = "SELECT cliente_id, agente_id FROM solicitudes_compra WHERE id = ?";
        $conversation = $this->db->selectOne($sql, [$solicitudId]);
        
        if (!$conversation) {
            return;
        }
        
        $users = [$conversation['cliente_id'], $conversation['agente_id']];
        
        foreach ($users as $userId) {
            if (isset($this->userConnections[$userId])) {
                $this->userConnections[$userId]->send(json_encode($data));
            }
        }
    }

    protected function broadcastToDirectConversation($conversationId, $data) {
        // Obtener usuarios de la conversación directa
        $sql = "SELECT cliente_id, agente_id FROM conversaciones_directas WHERE id = ?";
        $conversation = $this->db->selectOne($sql, [$conversationId]);
        
        if (!$conversation) {
            return;
        }
        
        $users = [$conversation['cliente_id'], $conversation['agente_id']];
        
        foreach ($users as $userId) {
            if (isset($this->userConnections[$userId])) {
                $this->userConnections[$userId]->send(json_encode($data));
            }
        }
    }
}

// Crear y ejecutar el servidor
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatWebSocket()
        )
    ),
    8080
);

echo "Servidor WebSocket iniciado en puerto 8080\n";
$server->run(); 