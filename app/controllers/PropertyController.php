<?php
/**
 * Controlador PropertyController - Gestión de Propiedades
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja todas las operaciones relacionadas con propiedades:
 * listado, creación, edición, validación, etc.
 */

require_once APP_PATH . '/models/Property.php';

class PropertyController {
    private $propertyModel;
    
    /**
     * Constructor del controlador
     */
    public function __construct() {
        $this->propertyModel = new Property();
    }
    
    /**
     * Mostrar listado de propiedades públicas
     */
    public function index() {
        $pageTitle = 'Propiedades - ' . APP_NAME;
        
        // Obtener filtros de la URL
        $filters = [
            'tipo' => $_GET['tipo'] ?? '',
            'ciudad' => $_GET['ciudad'] ?? '',
            'sector' => $_GET['sector'] ?? '',
            'precio_min' => $_GET['precio_min'] ?? '',
            'precio_max' => $_GET['precio_max'] ?? '',
            'habitaciones' => $_GET['habitaciones'] ?? '',
            'banos' => $_GET['banos'] ?? ''
        ];
        
        // Obtener página actual
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = 9; // 9 propiedades por página
        $offset = ($page - 1) * $limit;
        
        // Obtener total de propiedades para paginación (más eficiente)
        $totalProperties = $this->propertyModel->getTotalPropertiesWithFilters($filters);
        $totalPages = ceil($totalProperties / $limit);
        
        // Obtener propiedades de la página actual
        $properties = $this->propertyModel->getAll($filters, $limit, $offset);
        
        // Obtener estadísticas
        $stats = $this->propertyModel->getStats();
        
        include APP_PATH . '/views/properties/index.php';
    }
    
    /**
     * Mostrar detalle de una propiedad
     */
    public function show($id) {
        error_log("PropertyController::show - ID recibido: " . $id);
        
        $property = $this->propertyModel->getById($id);
        
        error_log("PropertyController::show - Propiedad encontrada: " . ($property ? 'SÍ' : 'NO'));
        
        if (!$property) {
            setFlashMessage('error', 'La propiedad solicitada no existe o ha sido eliminada.');
            redirect('/properties');
        }
        
        // Verificar que la propiedad esté activa para el público
        // Solo permitir acceso a propiedades activas para usuarios no autenticados
        if (!isAuthenticated()) {
            if ($property['estado_publicacion'] !== 'activa') {
                setFlashMessage('error', 'Esta propiedad no está disponible.');
                redirect('/properties');
            }
        } else {
            // Para usuarios autenticados, verificar permisos según el rol
            $userRole = $_SESSION['user_rol'];
            $userId = $_SESSION['user_id'];
            
            if ($userRole === 'cliente') {
                // Clientes solo pueden ver propiedades activas o sus propias propiedades
                if ($property['estado_publicacion'] !== 'activa' && $property['cliente_vendedor_id'] != $userId) {
                    setFlashMessage('error', 'Esta propiedad no está disponible.');
                    redirect('/properties');
                }
            } elseif ($userRole === 'agente') {
                // Agentes pueden ver propiedades activas o las asignadas a ellos
                if ($property['estado_publicacion'] !== 'activa' && $property['agente_id'] != $userId) {
                    setFlashMessage('error', 'Esta propiedad no está disponible.');
                    redirect('/properties');
                }
            }
            // Los administradores pueden ver todas las propiedades
        }
        
        $pageTitle = $property['titulo'] . ' - ' . APP_NAME;
        include APP_PATH . '/views/properties/show.php';
    }
    
    /**
     * Mostrar formulario para crear propiedad (cliente)
     */
    public function create() {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            setFlashMessage('error', 'Debes iniciar sesión para publicar una propiedad.');
            redirect('/login');
        }
        
        // Obtener agentes disponibles
        $agentes = $this->propertyModel->getAgentesDisponibles();
        
        $pageTitle = 'Publicar Propiedad - ' . APP_NAME;
        include APP_PATH . '/views/properties/create.php';
    }
    
    /**
     * Procesar creación de propiedad
     */
    public function store() {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            setFlashMessage('error', 'Debes iniciar sesión para publicar una propiedad.');
            redirect('/login');
        }
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/properties/create');
        }
        
        // Obtener datos del formulario
        $data = [
            'titulo' => $_POST['titulo'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'tipo' => $_POST['tipo'] ?? '',
            'precio' => $_POST['precio'] ?? '',
            'moneda' => 'USD', // Solo se permite USD
            'ciudad' => $_POST['ciudad'] ?? '',
            'sector' => $_POST['sector'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'metros_cuadrados' => $_POST['metros_cuadrados'] ?? '',
            'habitaciones' => $_POST['habitaciones'] ?? '',
            'banos' => $_POST['banos'] ?? '',
            'estacionamientos' => $_POST['estacionamientos'] ?? 0,
            'estado_propiedad' => $_POST['estado_propiedad'] ?? 'bueno',
            'cliente_vendedor_id' => $_SESSION['user_id']
        ];
        
        // Procesar selección de agente
        if (!empty($_POST['agente_id'])) {
            $data['agente_id'] = intval($_POST['agente_id']);
        }
        
        // Procesar imágenes si se subieron
        if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
            $data['imagenes'] = $this->processUploadedImages($_FILES['imagenes']);
        }
        
        // Crear propiedad
        $result = $this->propertyModel->create($data);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            
            // Mostrar el token y el agente asignado
            if (isset($result['token_validacion']) && isset($result['agente_asignado'])) {
                $agente = $result['agente_asignado'];
                $msg = '<div style="background: #f0f9ff; border: 1px solid #0ea5e9; padding: 15px; border-radius: 8px; margin: 10px 0;">';
                $msg .= '<h4 style="color: #0369a1; margin: 0 0 10px 0;"><i class="fas fa-key"></i> Token de Validación</h4>';
                $msg .= '<p style="margin: 5px 0;"><strong>Tu token único es:</strong></p>';
                $msg .= '<div style="background: #e0f2fe; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 14px; color: #0c4a6e; border: 1px solid #0284c7;">';
                $msg .= '<strong>' . $result['token_validacion'] . '</strong>';
                $msg .= '</div>';
                $msg .= '<p style="margin: 10px 0 5px 0; color: #0369a1;"><strong>Instrucciones:</strong></p>';
                $msg .= '<ul style="margin: 5px 0; color: #0369a1;">';
                $msg .= '<li>Comparte este token con el agente asignado</li>';
                $msg .= '<li>El agente lo utilizará para buscar e identificar tu propiedad</li>';
                $msg .= '<li>Una vez validada por el agente, tu propiedad aparecerá en el listado público</li>';
                $msg .= '</ul>';
                if ($agente) {
                    $msg .= '<p style="margin: 10px 0 0 0; color: #0369a1;"><strong>Agente asignado:</strong> ' . htmlspecialchars($agente['nombre'] . ' ' . $agente['apellido']) . ' (' . htmlspecialchars($agente['email']) . ')</p>';
                } else {
                    $msg .= '<p style="margin: 10px 0 0 0; color: #dc2626;"><strong>Nota:</strong> No se pudo asignar un agente automáticamente. Contacta al administrador.</p>';
                }
                $msg .= '</div>';
                setFlashMessage('info', $msg);
            }
            
            redirect('/properties');
        } else {
            setFlashMessage('error', $result['message']);
            if (isset($result['errors'])) {
                setFlashMessage('error', implode(', ', $result['errors']));
            }
            redirect('/properties/create');
        }
    }
    
    /**
     * Mostrar formulario para editar propiedad
     */
    public function edit($id) {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            setFlashMessage('error', 'Debes iniciar sesión para editar una propiedad.');
            redirect('/login');
        }
        
        $property = $this->propertyModel->getById($id);
        
        if (!$property) {
            setFlashMessage('error', 'Propiedad no encontrada.');
            redirect('/properties');
        }
        
        // Verificar permisos (solo el agente asignado o el cliente vendedor pueden editar)
        $userRole = $_SESSION['user_rol'];
        $userId = $_SESSION['user_id'];
        
        if ($userRole === 'cliente' && $property['cliente_vendedor_id'] != $userId) {
            setFlashMessage('error', 'No tienes permisos para editar esta propiedad.');
            redirect('/properties');
        }
        
        if ($userRole === 'agente' && $property['agente_id'] != $userId) {
            setFlashMessage('error', 'No tienes permisos para editar esta propiedad.');
            redirect('/properties');
        }
        
        $pageTitle = 'Editar Propiedad - ' . APP_NAME;
        include APP_PATH . '/views/properties/edit.php';
    }
    
    /**
     * Procesar actualización de propiedad
     */
    public function update($id) {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            setFlashMessage('error', 'Debes iniciar sesión para editar una propiedad.');
            redirect('/login');
        }
        
        // Verificar método POST o PUT
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/properties/' . $id . '/edit');
        }
        
        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/properties/' . $id . '/edit');
        }
        
        // Verificar que la propiedad existe
        $property = $this->propertyModel->getById($id);
        if (!$property) {
            setFlashMessage('error', 'Propiedad no encontrada.');
            redirect('/properties');
        }
        
        // Verificar permisos
        $userRole = $_SESSION['user_rol'];
        $userId = $_SESSION['user_id'];
        
        if ($userRole === 'cliente' && $property['cliente_vendedor_id'] != $userId) {
            setFlashMessage('error', 'No tienes permisos para editar esta propiedad.');
            redirect('/properties');
        }
        
        if ($userRole === 'agente' && $property['agente_id'] != $userId) {
            setFlashMessage('error', 'No tienes permisos para editar esta propiedad.');
            redirect('/properties');
        }
        
        // Obtener datos del formulario
        $data = [
            'titulo' => $_POST['titulo'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? '',
            'tipo' => $_POST['tipo'] ?? '',
            'precio' => $_POST['precio'] ?? '',
            'moneda' => 'USD', // Solo se permite USD
            'ciudad' => $_POST['ciudad'] ?? '',
            'sector' => $_POST['sector'] ?? '',
            'direccion' => $_POST['direccion'] ?? '',
            'metros_cuadrados' => $_POST['metros_cuadrados'] ?? '',
            'habitaciones' => $_POST['habitaciones'] ?? '',
            'banos' => $_POST['banos'] ?? '',
            'estacionamientos' => $_POST['estacionamientos'] ?? 0,
            'estado_propiedad' => $_POST['estado_propiedad'] ?? 'bueno'
        ];
        
        // Procesar nuevas imágenes si se subieron
        if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
            $data['imagenes'] = $this->processUploadedImages($_FILES['imagenes']);
        }
        
        // Actualizar propiedad
        $result = $this->propertyModel->update($id, $data);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            // Redirigir según el rol del usuario
            if (hasRole(ROLE_AGENTE)) {
                redirect('/properties/agent/list');
            } else {
                redirect('/properties/show/' . $id);
            }
        } else {
            setFlashMessage('error', $result['message']);
            if (isset($result['errors'])) {
                setFlashMessage('error', implode(', ', $result['errors']));
            }
            redirect('/properties/' . $id . '/edit');
        }
    }
    
    /**
     * Eliminar propiedad
     */
    public function delete($id) {
        // Verificar que el usuario esté autenticado
        if (!isAuthenticated()) {
            setFlashMessage('error', 'Debes iniciar sesión para eliminar una propiedad.');
            redirect('/login');
        }
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/properties');
        }
        
        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de seguridad inválido.');
            redirect('/properties/show/' . $id);
        }
        
        // Verificar que la propiedad existe
        $property = $this->propertyModel->getById($id);
        if (!$property) {
            setFlashMessage('error', 'Propiedad no encontrada.');
            redirect('/properties');
        }
        
        // Verificar permisos
        $userRole = $_SESSION['user_rol'];
        $userId = $_SESSION['user_id'];
        
        if ($userRole === 'cliente' && $property['cliente_vendedor_id'] != $userId) {
            setFlashMessage('error', 'No tienes permisos para eliminar esta propiedad.');
            redirect('/properties');
        }
        
        if ($userRole === 'agente' && $property['agente_id'] != $userId) {
            setFlashMessage('error', 'No tienes permisos para eliminar esta propiedad.');
            redirect('/properties');
        }
        
        // Eliminar propiedad
        $result = $this->propertyModel->delete($id);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
        } else {
            setFlashMessage('error', $result['message']);
        }
        
        redirect('/properties');
    }
    
    /**
     * Mostrar propiedades del agente
     */
    public function agentProperties() {
        // Verificar que el usuario esté autenticado y sea agente
        if (!isAuthenticated() || !hasRole(ROLE_AGENTE)) {
            setFlashMessage('error', 'Acceso denegado.');
            redirect('/');
        }
        
        $pageTitle = 'Mis Propiedades - ' . APP_NAME;
        
        // Obtener filtros
        $estado = $_GET['estado'] ?? null;
        
        // Obtener propiedades del agente
        $properties = $this->propertyModel->getByAgent($_SESSION['user_id'], $estado);
        
        include APP_PATH . '/views/properties/agent-properties.php';
    }
    
    /**
     * Mostrar propiedades pendientes de validación para el agente
     */
    public function pendingValidation() {
        // Verificar que el usuario esté autenticado y sea agente
        if (!isAuthenticated() || $_SESSION['user_rol'] !== 'agente') {
            setFlashMessage('error', 'Acceso denegado. Solo los agentes pueden acceder a esta sección.');
            redirect('/dashboard');
        }
        
        $agenteId = $_SESSION['user_id'];
        
        // Obtener filtro de búsqueda simple
        $search = $_GET['search'] ?? '';
        
        // Obtener propiedades pendientes con búsqueda
        $properties = $this->propertyModel->getPropiedadesPendientes($agenteId, $search);
        
        // Obtener estadísticas para el agente
        $stats = $this->propertyModel->getStatsByAgent($agenteId);
        
        $pageTitle = 'Propiedades Pendientes de Validación - ' . APP_NAME;
        include APP_PATH . '/views/properties/pending-validation.php';
    }
    
    /**
     * Validar propiedad (POST)
     */
    public function validate($id) {
        header('Content-Type: application/json');
        if (!isAuthenticated() || $_SESSION['user_rol'] !== 'agente') {
            echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }
        require_once APP_PATH . '/core/Database.php';
        $db = new Database();
        $query = "UPDATE propiedades SET estado_publicacion = 'activa' WHERE id = ?";
        $result = $db->update($query, [$id]);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Propiedad validada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al validar la propiedad.']);
        }
        exit;
    }
    

    
    /**
     * Rechazar propiedad (POST)
     */
    public function reject($id) {
        header('Content-Type: application/json');
        if (!isAuthenticated() || $_SESSION['user_rol'] !== 'agente') {
            echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }
        require_once APP_PATH . '/core/Database.php';
        $db = new Database();
        $query = "UPDATE propiedades SET estado_publicacion = 'rechazada' WHERE id = ?";
        $result = $db->update($query, [$id]);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Propiedad rechazada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al rechazar la propiedad.']);
        }
        exit;
    }
    
    /**
     * Mostrar formulario de rechazo (GET)
     */
    public function rejectForm($id) {
        // Verificar que el usuario esté autenticado y sea agente
        if (!isAuthenticated() || $_SESSION['user_rol'] !== 'agente') {
            setFlashMessage('error', 'Acceso denegado. Solo los agentes pueden rechazar propiedades.');
            redirect('/dashboard');
        }
        
        $property = $this->propertyModel->getById($id);
        
        if (!$property) {
            setFlashMessage('error', 'Propiedad no encontrada.');
            redirect('/properties/pending-validation');
        }
        
        // Verificar que la propiedad esté asignada al agente
        if ($property['agente_id'] != $_SESSION['user_id']) {
            setFlashMessage('error', 'No tienes permisos para rechazar esta propiedad.');
            redirect('/properties/pending-validation');
        }
        
        // Verificar que la propiedad esté en revisión
        if ($property['estado_publicacion'] !== 'en_revision') {
            setFlashMessage('error', 'Esta propiedad ya no está en revisión.');
            redirect('/properties/pending-validation');
        }
        
        $pageTitle = 'Rechazar Propiedad - ' . APP_NAME;
        include APP_PATH . '/views/properties/reject-form.php';
    }
    
    /**
     * Exportar propiedades pendientes a CSV
     */
    public function exportPendingToCSV() {
        // Verificar que el usuario esté autenticado y sea agente
        if (!isAuthenticated() || $_SESSION['user_rol'] !== 'agente') {
            setFlashMessage('error', 'Acceso denegado. Solo los agentes pueden exportar datos.');
            redirect('/dashboard');
        }
        
        $agenteId = $_SESSION['user_id'];
        $search = $_GET['search'] ?? '';
        
        // Obtener propiedades pendientes
        $properties = $this->propertyModel->getPropiedadesPendientes($agenteId, $search);
        
        // Configurar headers para descarga CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="propiedades_pendientes_' . date('Y-m-d_H-i-s') . '.csv"');
        
        // Crear archivo CSV
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers del CSV
        fputcsv($output, [
            'ID',
            'Título',
            'Tipo',
            'Precio',
            'Ciudad',
            'Sector',
            'Habitaciones',
            'Baños',
            'Metros Cuadrados',
            'Cliente Nombre',
            'Cliente Email',
            'Cliente Teléfono',
            'Token Validación',
            'Fecha Creación',
            'Estado'
        ]);
        
        // Datos de las propiedades
        foreach ($properties as $property) {
            fputcsv($output, [
                $property['id'],
                $property['titulo'],
                ucfirst(str_replace('_', ' ', $property['tipo'])),
                '$' . number_format($property['precio'], 2),
                $property['ciudad'],
                $property['sector'],
                $property['habitaciones'],
                $property['banos'],
                $property['metros_cuadrados'],
                $property['cliente_nombre'] . ' ' . $property['cliente_apellido'],
                $property['cliente_email'],
                $property['cliente_telefono'],
                $property['token_validacion'],
                date('d/m/Y H:i', strtotime($property['fecha_creacion'])),
                'En Revisión'
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Procesar imágenes subidas
     * 
     * @param array $files Array de archivos subidos
     * @return array Array procesado de imágenes
     */
    private function processUploadedImages($files) {
        $processedImages = [];
        
        // Crear directorio de uploads si no existe
        $uploadDir = PUBLIC_PATH . '/uploads/properties/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Procesar cada archivo
        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $files['name'][$i];
            $fileTmpName = $files['tmp_name'][$i];
            $fileSize = $files['size'][$i];
            $fileType = $files['type'][$i];
            $fileError = $files['error'][$i];
            
            if ($fileError !== UPLOAD_ERR_OK) {
                continue;
            }
            
            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($fileType, $allowedTypes)) {
                continue;
            }
            
            // Validar tamaño (máximo 5MB)
            if ($fileSize > 5 * 1024 * 1024) {
                continue;
            }
            
            // Generar nombre único con microsegundos para evitar duplicados
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $microtime = microtime(true);
            $uniqueName = uniqid() . '_' . str_replace('.', '', $microtime) . '.' . $extension;
            $filePath = $uploadDir . $uniqueName;
            
            // Verificar que el archivo temporal existe
            if (!file_exists($fileTmpName)) {
                continue;
            }
            
            // Mover archivo usando copy en lugar de move_uploaded_file para mayor compatibilidad
            if (copy($fileTmpName, $filePath)) {
                // Verificar que el archivo se copió correctamente
                if (file_exists($filePath)) {
                $processedImages[] = [
                    'name' => $uniqueName,
                    'original_name' => $fileName,
                    'path' => '/uploads/properties/' . $uniqueName,
                    'size' => $fileSize,
                    'type' => $fileType
                ];
                }
            }
        }
        
        return $processedImages;
    }
    

    
    /**
     * Actualizar estado de propiedad (AJAX)
     */
    public function updateStatus() {
        header('Content-Type: application/json');
        
        // Verificar que el usuario esté autenticado y sea agente
        if (!isAuthenticated() || !hasRole(ROLE_AGENTE)) {
            echo json_encode(['success' => false, 'message' => 'Acceso denegado.']);
            exit;
        }
        
        // Verificar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit;
        }
        
        // Verificar token CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido.']);
            exit;
        }
        
        $propertyId = $_POST['property_id'] ?? null;
        $newStatus = $_POST['new_status'] ?? null;
        $comment = $_POST['comment'] ?? '';
        
        if (!$propertyId || !$newStatus) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            exit;
        }
        
        // Verificar que la propiedad existe y está asignada al agente
        $property = $this->propertyModel->getById($propertyId);
        if (!$property) {
            echo json_encode(['success' => false, 'message' => 'Propiedad no encontrada.']);
            exit;
        }
        
        if ($property['agente_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar esta propiedad.']);
            exit;
        }
        
        // Validar estado
        $validStatuses = ['activa', 'vendida', 'en_revision', 'rechazada'];
        if (!in_array($newStatus, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Estado no válido.']);
            exit;
        }
        
        // Actualizar estado
        $result = $this->propertyModel->updateStatus($propertyId, $newStatus, $comment);
        
        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
        exit;
    }
    
    /**
     * API: Listar propiedades en formato JSON
     */
    public function list() {
        header('Content-Type: application/json');
        
        try {
            $properties = $this->propertyModel->getAllActive();
            echo json_encode([
                'success' => true,
                'properties' => $properties
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al cargar propiedades'
            ]);
        }
    }
} 
