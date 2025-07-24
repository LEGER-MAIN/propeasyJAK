<?php
/**
 * Controlador HomeController - Página Principal
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja la página principal y otras páginas públicas.
 */

require_once APP_PATH . '/models/Property.php';
require_once APP_PATH . '/models/Favorite.php';
require_once APP_PATH . '/models/User.php';

class HomeController {
    
    /**
     * Mostrar página principal
     */
    public function index() {
        // Cargar modelos
        $propertyModel = new Property();
        $favoriteModel = new Favorite();
        $userModel = new User();
        
        // Obtener estadísticas reales
        $stats = [
            'total_propiedades' => $propertyModel->getTotalPropiedades(),
            'propiedades_activas' => $propertyModel->getTotalPropiedadesActivas(),
            'total_agentes' => $userModel->getTotalUsuariosPorRol('agente'),
            'total_clientes' => $userModel->getTotalUsuariosPorRol('cliente')
        ];
        
        // Obtener propiedades más favoritas
        $propiedadesDestacadas = $favoriteModel->getPropiedadesMasFavoritas(6);
        
        // Si no hay propiedades con favoritos, cargar las más recientes
        if (empty($propiedadesDestacadas)) {
            $propiedadesDestacadas = $propertyModel->getPropiedadesRecientes(6);
        }
        
        // Establecer variables para la vista
        $pageTitle = 'Inicio - ' . APP_NAME;
        
        // Incluir la vista directamente (sin capturar contenido)
        include APP_PATH . '/views/home/index.php';
    }
    
    /**
     * Mostrar página "Acerca de"
     */
    public function about() {
        $pageTitle = 'Acerca de - ' . APP_NAME;
        include APP_PATH . '/views/home/about.php';
    }
    
    /**
     * Mostrar página de contacto
     */
    public function contact() {
        $pageTitle = 'Contacto - ' . APP_NAME;
        include APP_PATH . '/views/home/contact.php';
    }
    
    /**
     * Mostrar página de términos y condiciones
     */
    public function terms() {
        $pageTitle = 'Términos y Condiciones - ' . APP_NAME;
        include APP_PATH . '/views/home/terms.php';
    }
    
    /**
     * Mostrar página de política de privacidad
     */
    public function privacy() {
        $pageTitle = 'Política de Privacidad - ' . APP_NAME;
        include APP_PATH . '/views/home/privacy.php';
    }
} 
