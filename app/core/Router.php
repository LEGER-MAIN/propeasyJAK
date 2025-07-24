<?php
/**
 * Clase Router - Enrutamiento de la Aplicación
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Esta clase maneja el enrutamiento de todas las URLs de la aplicación,
 * mapeando las rutas a los controladores y métodos correspondientes.
 */

class Router {
    private $routes = [];
    private $notFoundCallback;
    
    /**
     * Constructor del router
     */
    public function __construct() {
        // Configurar callback para rutas no encontradas
        $this->notFoundCallback = function() {
            http_response_code(404);
            include APP_PATH . '/views/errors/404.php';
        };
        
        // Configurar todas las rutas
        $this->configureRoutes();
    }
    
    /**
     * Registrar una ruta GET
     * 
     * @param string $path Ruta de la URL
     * @param callable|string $callback Callback o string del controlador
     */
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    
    /**
     * Registrar una ruta POST
     * 
     * @param string $path Ruta de la URL
     * @param callable|string $callback Callback o string del controlador
     */
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    
    /**
     * Registrar una ruta PUT
     * 
     * @param string $path Ruta de la URL
     * @param callable|string $callback Callback o string del controlador
     */
    public function put($path, $callback) {
        $this->routes['PUT'][$path] = $callback;
    }
    
    /**
     * Registrar una ruta DELETE
     * 
     * @param string $path Ruta de la URL
     * @param callable|string $callback Callback o string del controlador
     */
    public function delete($path, $callback) {
        $this->routes['DELETE'][$path] = $callback;
    }
    
    /**
     * Registrar callback para rutas no encontradas
     * 
     * @param callable $callback Callback a ejecutar
     */
    public function notFound($callback) {
        $this->notFoundCallback = $callback;
    }
    
    /**
     * Ejecutar el router
     */
    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $this->getCurrentPath();
        
        // Verificar si es un archivo estático (uploads, css, js, etc.)
        if ($this->isStaticFile($path)) {
            $this->serveStaticFile($path);
            return;
        }
        
        // Verificar si existe la ruta
        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
            $this->executeCallback($callback);
        } else {
            // Buscar rutas con parámetros
            $route = $this->findRouteWithParams($method, $path);
            if ($route) {
                $this->executeCallback($route['callback'], $route['params']);
            } else {
                // Ruta no encontrada
                call_user_func($this->notFoundCallback);
            }
        }
    }
    
    /**
     * Obtener la ruta actual
     * 
     * @return string Ruta actual
     */
    private function getCurrentPath() {
        $path = $_SERVER['REQUEST_URI'];
        
        // Remover query string
        $path = parse_url($path, PHP_URL_PATH);
        
        // Para Laragon que sirve desde public/, no necesitamos remover directorio base
        // ya que $_SERVER['SCRIPT_NAME'] ya es /index.php
        
        // Asegurar que empiece con /
        if (empty($path)) {
            $path = '/';
        }
        
        return $path;
    }
    
    /**
     * Verificar si la ruta es un archivo estático
     * 
     * @param string $path Ruta de la URL
     * @return bool True si es un archivo estático
     */
    private function isStaticFile($path) {
        // Verificar si la ruta comienza con /uploads/, /css/, /js/, /images/
        $staticPaths = ['/uploads/', '/css/', '/js/', '/images/'];
        foreach ($staticPaths as $staticPath) {
            if (strpos($path, $staticPath) === 0) {
                return true;
            }
        }
        
        // Verificar si termina con una extensión de archivo estático
        $staticExtensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.pdf', '.txt'];
        foreach ($staticExtensions as $ext) {
            if (strpos($path, $ext) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Servir archivo estático
     * 
     * @param string $path Ruta del archivo
     */
    private function serveStaticFile($path) {
        $filePath = __DIR__ . '/../../public' . $path;
        
        // Verificar que el archivo existe y está dentro del directorio public
        if (!file_exists($filePath) || !is_file($filePath)) {
            http_response_code(404);
            return;
        }
        
        // Verificar que el archivo está dentro del directorio public (seguridad)
        $realPath = realpath($filePath);
        $publicPath = realpath(__DIR__ . '/../../public');
        if (strpos($realPath, $publicPath) !== 0) {
            http_response_code(403);
            return;
        }
        
        // Determinar el tipo MIME
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain'
        ];
        
        $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        // Servir el archivo
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=31536000'); // Cache por 1 año
        
        readfile($filePath);
    }
    
    /**
     * Buscar ruta con parámetros
     * 
     * @param string $method Método HTTP
     * @param string $path Ruta actual
     * @return array|false Array con callback y parámetros o false
     */
    private function findRouteWithParams($method, $path) {
        if (!isset($this->routes[$method])) {
            return false;
        }
        
        // Primero buscar rutas exactas (sin parámetros)
        foreach ($this->routes[$method] as $route => $callback) {
            if (strpos($route, '{') === false) {
                // Es una ruta exacta
                if ($route === $path) {
                    return [
                        'callback' => $callback,
                        'params' => []
                    ];
                }
            }
        }
        
        // Luego buscar rutas con parámetros
        foreach ($this->routes[$method] as $route => $callback) {
            if (strpos($route, '{') !== false) {
                // Es una ruta con parámetros
                $pattern = $this->convertRouteToRegex($route);
                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches); // Remover el match completo
                    return [
                        'callback' => $callback,
                        'params' => $matches
                    ];
                }
            }
        }
        
        return false;
    }
    
    /**
     * Convertir ruta a expresión regular
     * 
     * @param string $route Ruta con parámetros
     * @return string Expresión regular
     */
    private function convertRouteToRegex($route) {
        // Convertir parámetros {param} a grupos de captura
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Ejecutar callback
     * 
     * @param callable|string $callback Callback a ejecutar
     * @param array $params Parámetros adicionales
     */
    private function executeCallback($callback, $params = []) {
        if (is_callable($callback)) {
            call_user_func_array($callback, $params);
        } elseif (is_string($callback)) {
            $this->executeControllerMethod($callback, $params);
        }
    }
    
    /**
     * Ejecutar método de controlador
     * 
     * @param string $controllerMethod String del controlador (ej: "AuthController@login")
     * @param array $params Parámetros adicionales
     */
    private function executeControllerMethod($controllerMethod, $params = []) {
        $parts = explode('@', $controllerMethod);
        
        if (count($parts) !== 2) {
            throw new Exception('Formato de controlador inválido. Use: Controller@method');
        }
        
        $controllerName = $parts[0];
        $methodName = $parts[1];
        
        // Verificar si existe el archivo del controlador
        $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            throw new Exception("Controlador no encontrado: {$controllerName}");
        }
        
        // Incluir el archivo del controlador
        require_once $controllerFile;
        
        // Verificar si existe la clase
        if (!class_exists($controllerName)) {
            throw new Exception("Clase no encontrada: {$controllerName}");
        }
        
        // Crear instancia del controlador
        $controller = new $controllerName();
        
        // Verificar si existe el método
        if (!method_exists($controller, $methodName)) {
            throw new Exception("Método no encontrado: {$controllerName}::{$methodName}");
        }
        
        // Ejecutar el método
        call_user_func_array([$controller, $methodName], $params);
    }
    
    /**
     * Obtener todas las rutas (para debugging)
     */
    public function getRoutes() {
        return $this->routes;
    }
    
    /**
     * Configurar todas las rutas de la aplicación
     */
    public function configureRoutes() {
        // Rutas públicas
        $this->get('/', 'HomeController@index');
        $this->get('/about', 'HomeController@about');
        
        // Rutas de autenticación
        $this->get('/login', 'AuthController@showLogin');
        $this->post('/login', 'AuthController@login');
        $this->get('/register', 'AuthController@showRegister');
        $this->post('/register', 'AuthController@register');
        $this->get('/verify-email', 'AuthController@verifyEmail');
        $this->get('/forgot-password', 'AuthController@showForgotPassword');
        $this->post('/forgot-password', 'AuthController@forgotPassword');
        $this->get('/reset-password', 'AuthController@showResetPassword');
        $this->post('/reset-password', 'AuthController@resetPassword');
        $this->get('/logout', 'AuthController@logout');
        $this->get('/auth/check', 'AuthController@check');
        
        // Rutas protegidas (requieren autenticación)
        $this->get('/dashboard', 'DashboardController@index');
        $this->get('/profile', 'ProfileController@showProfile');
        $this->post('/profile', 'ProfileController@updateProfile');
        
        // Rutas de administrador - Panel de Control Total
        $this->get('/admin/dashboard', 'AdminController@dashboard');
        $this->get('/admin/users', 'AdminController@manageUsers');
        $this->post('/admin/users', 'AdminController@manageUsers');
        $this->get('/admin/users/change-role/{id}', 'AdminController@showChangeRoleForm');
        $this->post('/admin/users/change-role/{id}', 'AdminController@changeUserRole');
        $this->post('/admin/users/change-status', 'AdminController@changeUserStatus');
        $this->post('/admin/users/block', 'AdminController@blockUser');
        $this->post('/admin/users/unblock', 'AdminController@unblockUser');
        $this->post('/admin/users/delete', 'AdminController@deleteUser');
        
        // Gestión de propiedades
        $this->get('/admin/properties', 'AdminController@manageProperties');
        $this->post('/admin/properties/approve', 'AdminController@approveProperty');
        $this->post('/admin/properties/reject', 'AdminController@rejectProperty');

        $this->post('/admin/properties/delete', 'AdminController@deleteProperty');
        

        
        // Gestión de Reportes
        $this->get('/admin/reports', 'AdminController@manageReports');
        $this->get('/admin/reports/view/{id}', 'AdminController@viewReport');
        $this->get('/admin/reports/{action}/{id}', 'AdminController@manageReports');
        $this->post('/admin/reports', 'AdminController@manageReports');
        

        
        // Endpoint AJAX para datos de gráficos
        $this->get('/admin/chart-data', 'AdminController@getChartData');
        
        // Actividades del sistema
        $this->get('/admin/activities', 'AdminController@allActivities');
        
        // Gestión de alertas del sistema
        $this->post('/admin/alerts/dismiss', 'AdminController@dismissAlert');
        

        
        // Rutas de agente
        $this->get('/agente/dashboard', 'AgenteController@showDashboard');
        $this->get('/agente/perfil', 'AgenteController@showPerfil');
        $this->post('/agente/perfil', 'AgenteController@updatePerfil');
        $this->get('/agente/perfil-publico', 'AgenteController@showPerfilPublico');
        $this->post('/agente/perfil-publico', 'AgenteController@updatePerfilPublico');
        $this->get('/agente/propiedades', 'AgenteController@propiedadesPendientes');
        $this->post('/agente/propiedades/aprobar', 'AgenteController@aprobarPropiedad');
        $this->post('/agente/propiedades/rechazar', 'AgenteController@rechazarPropiedad');
        $this->post('/agente/propiedades/eliminar', 'AgenteController@eliminarPropiedad');
        
        // Rutas de perfil público del agente (vistas públicas)
        $this->get('/agente/{id}/perfil', 'AgenteController@perfilPublico');
        $this->get('/agentes', 'AgenteController@listarAgentes');
        
        // Rutas del cliente
        $this->get('/cliente/dashboard', 'ClienteController@showDashboard');
        $this->get('/cliente/perfil', 'ClienteController@showPerfil');
        $this->post('/cliente/perfil', 'ClienteController@updatePerfil');
        $this->get('/cliente/historial', 'ClienteController@showHistorial');
        $this->get('/cliente/configuracion', 'ClienteController@showConfiguracion');
        $this->post('/cliente/configuracion', 'ClienteController@updateConfiguracion');
        $this->get('/cliente/mis-propiedades-ajax', 'ClienteController@ajaxMisPropiedades');
        $this->post('/cliente/eliminar-solicitud', 'ClienteController@eliminarSolicitud');
        $this->get('/cliente/mis-ventas', 'ClienteController@misVentas');
        
        // Rutas de propiedades
        $this->get('/properties', 'PropertyController@index');
        $this->get('/properties/list', 'PropertyController@list');
        $this->get('/properties/show/{id}', 'PropertyController@show');
        $this->get('/properties/create', 'PropertyController@create');
        $this->post('/properties', 'PropertyController@store');
        $this->get('/properties/{id}/edit', 'PropertyController@edit');
        $this->post('/properties/{id}', 'PropertyController@update');
        $this->post('/properties/{id}/delete', 'PropertyController@delete');
        $this->post('/properties/update-status', 'PropertyController@updateStatus');
        
        // Rutas específicas de agentes para propiedades
        $this->get('/properties/agent/list', 'PropertyController@agentProperties');
        $this->get('/properties/pending-validation', 'PropertyController@pendingValidation');
        $this->get('/properties/pending-validation/export', 'PropertyController@exportPendingToCSV');
        $this->post('/properties/{id}/validate', 'PropertyController@validate');

        $this->post('/properties/{id}/reject', 'PropertyController@reject');
        $this->get('/properties/{id}/reject-form', 'PropertyController@rejectForm');
        
        // Rutas simples para pruebas
        $this->get('/simple/pending', 'SimpleController@pending');
        $this->post('/simple/activate', 'SimpleController@activate');
        $this->post('/simple/reject', 'SimpleController@reject');
        
        // Rutas de solicitudes de compra
        $this->get('/solicitudes', 'SolicitudController@index');
        $this->get('/solicitudes/create/{id}', 'SolicitudController@show');
        $this->post('/solicitudes', 'SolicitudController@store');
        $this->get('/solicitudes/{id}', 'SolicitudController@showSolicitud');
        $this->post('/solicitudes/{id}/update-status', 'SolicitudController@updateStatus');
        $this->post('/solicitudes/{id}/delete', 'SolicitudController@delete');
        $this->get('/api/solicitudes-cliente/{id}', 'SolicitudController@getSolicitudesCliente');
        $this->get('/api/solicitudes/stats', 'SolicitudController@getStats');
        
        // Rutas de chat
        $this->get('/chat', 'ChatController@index');
        
        // Chat simple desde cero (DEBE IR ANTES que /chat/{id})
        $this->get('/chat-simple', 'ChatController@simple');
        $this->get('/chat/simple', 'ChatController@simple');
        
        // Rutas del chat integrado (API)
        $this->get('/chat/conversations', 'ChatController@conversations');
        $this->get('/chat/messages/{id}', 'ChatController@messages');
        $this->post('/chat/send-message', 'ChatController@sendMessageJson');
        $this->post('/chat/create-conversation', 'ChatController@createConversation');
        
        // Rutas del chat directo (sin solicitudes de compra)
        $this->get('/chat/direct-conversations', 'ChatController@directConversations');
        $this->get('/chat/direct/{id}/messages', 'ChatController@directMessages');
        $this->post('/chat/send-direct-message', 'ChatController@sendDirectMessage');
        $this->post('/chat/create-direct-conversation', 'ChatController@createDirectConversation');
        $this->get('/chat/users-for-direct-chat', 'ChatController@usersForDirectChat');
        
        // Rutas específicas del chat (DEBEN IR ANTES que /chat/{id})
        $this->get('/chat/unread-messages', 'ChatController@getUnreadMessages');
        $this->get('/chat/unread-count', 'ChatController@unreadCount');
        $this->get('/chat/stats', 'ChatController@getStats');
        $this->get('/chat/search', 'ChatController@search');
        $this->get('/chat/search-users', 'ChatController@searchUsers');
        $this->get('/chat/iniciar/{clienteId}', 'ChatController@iniciarConNuevoCliente');
        
        // Rutas de chat con parámetros (DEBEN IR AL FINAL)
        $this->get('/chat/{id}', 'ChatController@showDirectChat');
        $this->post('/chat/{id}/messages', 'ChatController@sendMessage');
        $this->get('/chat/{id}/messages', 'ChatController@getMessages');
        $this->post('/chat/{id}/mark-read', 'ChatController@markAsRead');
        
        // Rutas de favoritos
        $this->get('/favorites', 'FavoriteController@index');
        $this->post('/favorites/agregar', 'FavoriteController@agregar');
        $this->post('/favorites/eliminar', 'FavoriteController@eliminar');
        $this->post('/favorites/toggle', 'FavoriteController@toggle');
        $this->post('/favorites/eliminar-por-id', 'FavoriteController@eliminarPorId');
        $this->post('/favorites/limpiar-todos', 'FavoriteController@limpiarTodos');
        $this->get('/favorites/contador', 'FavoriteController@contador');
        $this->get('/favorites/verificar', 'FavoriteController@verificar');
        $this->get('/favorites/estadisticas', 'FavoriteController@estadisticas');
        
        // Rutas de búsqueda
        $this->get('/buscar-agentes', 'SearchController@buscarAgentes');
        $this->get('/buscar-clientes', 'SearchController@buscarClientes');
        $this->get('/api/buscar-agentes', 'SearchController@apiBuscarAgentes');
        $this->get('/api/buscar-clientes', 'SearchController@apiBuscarClientes');
        $this->get('/api/ciudades', 'SearchController@getCiudades');
        $this->get('/api/solicitudes-cliente/{id}', 'SearchController@getSolicitudesCliente');
        
        // Rutas de citas
        $this->get('/appointments', 'AppointmentController@index');
        $this->get('/appointments/create', 'AppointmentController@create');
        $this->post('/appointments/store', 'AppointmentController@store');
        $this->get('/appointments/{id}', 'AppointmentController@show');
        $this->get('/appointments/{id}/edit', 'AppointmentController@edit');
        $this->post('/appointments/{id}/update', 'AppointmentController@update');
        $this->post('/appointments/{id}/accept', 'AppointmentController@accept');
        $this->post('/appointments/{id}/reject', 'AppointmentController@reject');
        $this->post('/appointments/{id}/request-change', 'AppointmentController@requestChange');
        $this->post('/appointments/{id}/cancel', 'AppointmentController@cancel');
        $this->post('/appointments/{id}/complete', 'AppointmentController@complete');
        $this->get('/appointments/calendar', 'AppointmentController@calendar');
        $this->get('/api/appointments', 'AppointmentController@getAppointments');
        $this->get('/api/appointments/pending', 'AppointmentController@getPendingAppointments');
        
        // Rutas de reportes de irregularidades
        $this->get('/reportes/crear', 'ReporteController@crear');
        $this->post('/reportes/guardar', 'ReporteController@guardar');
        $this->get('/reportes/mis-reportes', 'ReporteController@misReportes');
        $this->get('/reportes/mostrar/{id}', 'ReporteController@mostrar');
        
        // Rutas de reportes de citas (solo administradores)
        $this->get('/reportes/citas', 'ReporteController@citas');
        
        // API routes
        $this->get('/api/properties', 'ApiController@properties');
        $this->get('/api/properties/{id}', 'ApiController@property');
        $this->post('/api/requests', 'ApiController@createRequest');
        $this->get('/api/stats', 'ApiController@stats');
        $this->get('/api/cities', 'ApiController@cities');
        $this->get('/api/property-types', 'ApiController@propertyTypes');
        $this->get('/api/search-properties', 'ApiController@searchProperties');
        $this->get('/api/agents', 'ApiController@agents');
        $this->get('/api/agents/{id}/profile', 'ApiController@agentProfile');
        
        // Configurar página 404
        $this->notFound(function() {
            http_response_code(404);
            $pageTitle = 'Página no encontrada - ' . APP_NAME;
            include APP_PATH . '/views/errors/404.php';
        });
    }
} 
