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
        
        // Remover directorio base si existe
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/') {
            $path = str_replace($scriptName, '', $path);
        }
        
        // Asegurar que empiece con /
        if (empty($path)) {
            $path = '/';
        }
        
        return $path;
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
        
        foreach ($this->routes[$method] as $route => $callback) {
            $pattern = $this->convertRouteToRegex($route);
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remover el match completo
                return [
                    'callback' => $callback,
                    'params' => $matches
                ];
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
     * Configurar todas las rutas de la aplicación
     */
    public function configureRoutes() {
        // Rutas públicas
        $this->get('/', 'HomeController@index');
        
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
        $this->get('/profile', 'AuthController@showProfile');
        $this->post('/profile', 'AuthController@updateProfile');
        
        // Rutas de administrador
        $this->get('/admin/dashboard', 'AdminController@dashboard');
        $this->get('/admin/users', 'AuthController@showUsers');
        $this->post('/admin/users/change-status', 'AuthController@changeUserStatus');
        
        // Rutas de agente
        $this->get('/agente/dashboard', 'AgenteController@dashboard');
        $this->get('/agente/propiedades', 'AgenteController@propiedadesPendientes');
        $this->post('/agente/propiedades/aprobar', 'AgenteController@aprobarPropiedad');
        $this->post('/agente/propiedades/rechazar', 'AgenteController@rechazarPropiedad');
        $this->post('/agente/propiedades/eliminar', 'AgenteController@eliminarPropiedad');
        
        // Rutas de propiedades
        $this->get('/properties', 'PropertyController@index');
        $this->get('/properties/list', 'PropertyController@list');
        $this->get('/properties/show/{id}', 'PropertyController@show');
        $this->get('/properties/create', 'PropertyController@create');
        $this->post('/properties', 'PropertyController@store');
        $this->get('/properties/{id}/edit', 'PropertyController@edit');
        $this->post('/properties/{id}', 'PropertyController@update');
        $this->post('/properties/{id}/delete', 'PropertyController@delete');
        
        // Rutas específicas de agentes para propiedades
        $this->get('/properties/agent/list', 'PropertyController@agentProperties');
        $this->get('/properties/pending-validation', 'PropertyController@pendingValidation');
        $this->get('/properties/pending-validation/export', 'PropertyController@exportPendingToCSV');
        $this->post('/properties/{id}/validate', 'PropertyController@validate');
        $this->post('/properties/{id}/reject', 'PropertyController@reject');
        $this->post('/properties/{id}/delete', 'PropertyController@deleteProperty');
        $this->get('/properties/{id}/reject-form', 'PropertyController@rejectForm');
        
        // Rutas simples para pruebas
        $this->get('/simple/pending', 'SimpleController@pending');
        $this->post('/simple/activate', 'SimpleController@activate');
        $this->post('/simple/reject', 'SimpleController@reject');
        
        // Rutas de solicitudes (futuras)
        $this->get('/requests', 'RequestController@index');
        $this->post('/requests', 'RequestController@store');
        $this->get('/requests/{id}', 'RequestController@show');
        
        // Rutas de chat
        $this->get('/chat', 'ChatController@index');
        $this->get('/chat/{id}', 'ChatController@show');
        $this->post('/chat/{id}/messages', 'ChatController@sendMessage');
        $this->get('/chat/{id}/messages', 'ChatController@getMessages');
        $this->get('/chat/unread-messages', 'ChatController@getUnreadMessages');
        $this->post('/chat/{id}/mark-read', 'ChatController@markAsRead');
        $this->get('/chat/stats', 'ChatController@getStats');
        $this->get('/chat/search', 'ChatController@search');
        $this->get('/chat/search-users', 'ChatController@searchUsers');
        $this->get('/chat/iniciar/{clienteId}', 'ChatController@iniciarConNuevoCliente');
        
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
        
        // Ruta de prueba para favoritos
        $this->get('/test-favorites', function() {
            include APP_PATH . '/../test_favorites.html';
        });
        
        // Ruta de prueba del chat real
        $this->get('/test-chat-real', function() {
            include APP_PATH . '/../test_chat_real.php';
        });
        
        // Ruta de prueba JavaScript simple
        $this->get('/test-js-simple', function() {
            include APP_PATH . '/../test_js_simple.php';
        });
        

        

        

        
        // Rutas de búsqueda
        $this->get('/buscar-agentes', 'SearchController@buscarAgentes');
        $this->get('/buscar-clientes', 'SearchController@buscarClientes');
        $this->get('/api/buscar-agentes', 'SearchController@apiBuscarAgentes');
        $this->get('/api/buscar-clientes', 'SearchController@apiBuscarClientes');
        $this->get('/api/ciudades', 'SearchController@getCiudades');
        $this->get('/api/solicitudes-cliente/{id}', 'SearchController@getSolicitudesCliente');
        
        // Rutas de citas (futuras)
        $this->get('/appointments', 'AppointmentController@index');
        $this->post('/appointments', 'AppointmentController@store');
        $this->get('/appointments/{id}', 'AppointmentController@show');
        $this->post('/appointments/{id}/accept', 'AppointmentController@accept');
        $this->post('/appointments/{id}/reject', 'AppointmentController@reject');
        
        // Rutas de reportes (futuras)
        $this->get('/reports', 'ReportController@index');
        $this->post('/reports', 'ReportController@store');
        $this->get('/reports/{id}', 'ReportController@show');
        
        // API routes (futuras)
        $this->get('/api/properties', 'ApiController@properties');
        $this->get('/api/properties/{id}', 'ApiController@property');
        $this->post('/api/requests', 'ApiController@createRequest');
        
        // Configurar página 404
        $this->notFound(function() {
            http_response_code(404);
            $pageTitle = 'Página no encontrada - ' . APP_NAME;
            include APP_PATH . '/views/errors/404.php';
        });
    }
} 