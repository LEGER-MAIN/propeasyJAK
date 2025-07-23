<?php
/**
 * Controlador ProfileController - Gestión Unificada de Perfiles
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja la edición de perfiles para todos los tipos de usuarios
 * (clientes, agentes, administradores) con funcionalidad completa.
 */

require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Favorite.php';
require_once APP_PATH . '/models/SolicitudCompra.php';
require_once APP_PATH . '/models/Property.php';

class ProfileController {
    private $userModel;
    private $favoriteModel;
    private $solicitudModel;
    private $propertyModel;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->userModel = new User();
        $this->favoriteModel = new Favorite();
        $this->solicitudModel = new SolicitudCompra();
        $this->propertyModel = new Property();
    }
    
    /**
     * Mostrar perfil del usuario
     */
    public function showProfile() {
        requireAuth();
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_rol'];
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            setFlashMessage('error', 'Usuario no encontrado.');
            redirect('/dashboard');
        }
        
        // Actualizar foto de perfil en la sesión si existe
        if (!empty($user['foto_perfil']) && empty($_SESSION['user_foto_perfil'])) {
            $_SESSION['user_foto_perfil'] = $user['foto_perfil'];
        }
        
        // Obtener estadísticas según el rol
        $stats = $this->getUserStats($userId, $userRole);
        
        // Obtener actividad reciente
        $actividadReciente = $this->getActividadReciente($userId, $userRole, 5);
        
        $pageTitle = 'Mi Perfil - ' . APP_NAME;
        $csrfToken = generateCSRFToken();
        
        // Capturar el contenido de la vista
        ob_start();
        include APP_PATH . '/views/profile/index.php';
        $content = ob_get_clean();
        
        // Incluir el layout principal
        include APP_PATH . '/views/layouts/main.php';
    }
    
    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile() {
        requireAuth();
        
        // Verificar método HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profile');
        }
        
        // Verificar CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/profile');
        }
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_rol'];
        
        // Obtener datos del formulario
        $userData = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'ciudad' => trim($_POST['ciudad'] ?? ''),
            'sector' => trim($_POST['sector'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ];
        
        // Campos específicos para agentes
        if ($userRole === 'agente') {
            $userData['experiencia_anos'] = (int)($_POST['experiencia_anos'] ?? 0);
            $userData['especialidades'] = trim($_POST['especialidades'] ?? '');
            $userData['idiomas'] = trim($_POST['idiomas'] ?? '');
            $userData['biografia'] = trim($_POST['biografia'] ?? '');
            $userData['licencia_inmobiliaria'] = trim($_POST['licencia_inmobiliaria'] ?? '');
            $userData['horario_disponibilidad'] = trim($_POST['horario_disponibilidad'] ?? '');
        }
        
        // Validar confirmación de contraseña si se proporciona
        if (!empty($userData['password'])) {
            if ($userData['password'] !== $userData['confirm_password']) {
                setFlashMessage('error', 'Las contraseñas no coinciden.');
                redirect('/profile');
            }
            
            // Validar longitud mínima de contraseña
            if (strlen($userData['password']) < 8) {
                setFlashMessage('error', 'La contraseña debe tener al menos 8 caracteres.');
                redirect('/profile');
            }
        } else {
            // Si no se proporciona contraseña, remover del array
            unset($userData['password']);
            unset($userData['confirm_password']);
        }
        
        // Procesar foto de perfil si se subió
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $fotoResult = $this->procesarFotoPerfil($_FILES['foto_perfil']);
            if ($fotoResult['success']) {
                $userData['foto_perfil'] = $fotoResult['ruta'];
            } else {
                setFlashMessage('error', $fotoResult['message']);
                redirect('/profile');
            }
        }
        
        // Actualizar perfil
        $result = $this->userModel->updateProfile($userId, $userData);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            
            // Actualizar datos de sesión
            if (isset($userData['nombre'])) {
                $_SESSION['user_nombre'] = $userData['nombre'];
            }
            if (isset($userData['apellido'])) {
                $_SESSION['user_apellido'] = $userData['apellido'];
            }
            if (isset($userData['foto_perfil'])) {
                $_SESSION['user_foto_perfil'] = $userData['foto_perfil'];
            }
        } else {
            setFlashMessage('error', $result['message']);
        }
        
        redirect('/profile');
    }
    
    /**
     * Procesar subida de foto de perfil
     * 
     * @param array $file Archivo subido
     * @return array Resultado del procesamiento
     */
    private function procesarFotoPerfil($file) {
        try {
            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                return [
                    'success' => false,
                    'message' => 'Solo se permiten archivos JPG, PNG y GIF.'
                ];
            }
            
            // Validar tamaño (máximo 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                return [
                    'success' => false,
                    'message' => 'El archivo es demasiado grande. Máximo 5MB.'
                ];
            }
            
            // Crear directorio si no existe
            $uploadDir = UPLOAD_PATH . '/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Mover archivo
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return [
                    'success' => true,
                    'ruta' => '/uploads/profiles/' . $filename,
                    'message' => 'Foto de perfil subida exitosamente.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al subir el archivo.'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error procesando foto de perfil: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor.'
            ];
        }
    }
    
    /**
     * Obtener estadísticas del usuario según su rol
     * 
     * @param int $userId ID del usuario
     * @param string $userRole Rol del usuario
     * @return array Estadísticas
     */
    private function getUserStats($userId, $userRole) {
        $stats = [];
        
        if ($userRole === 'cliente') {
            // Estadísticas para clientes
            $stats['favoritos'] = $this->favoriteModel->getCountByUser($userId);
            $stats['solicitudes'] = $this->solicitudModel->getCountByUser($userId);
        } elseif ($userRole === 'agente') {
            // Estadísticas para agentes
            $stats['propiedades_activas'] = $this->propertyModel->getCountByAgent($userId, 'activa');
            $stats['propiedades_vendidas'] = $this->propertyModel->getCountByAgent($userId, 'vendida');
            $stats['solicitudes_pendientes'] = $this->solicitudModel->getCountPendingByAgent($userId);
            $stats['total_ventas'] = $this->propertyModel->getTotalSalesByAgent($userId);
        }
        
        return $stats;
    }
    
    /**
     * Obtener actividad reciente del usuario
     * 
     * @param int $userId ID del usuario
     * @param string $userRole Rol del usuario
     * @param int $limit Límite de resultados
     * @return array Actividad reciente
     */
    private function getActividadReciente($userId, $userRole, $limit = 5) {
        // Por ahora retornamos un array vacío, se puede implementar después
        return [];
    }
}
?> 