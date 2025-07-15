<?php
/**
 * Controlador HomeController - Página Principal
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 * 
 * Este controlador maneja la página principal y otras páginas públicas.
 */

class HomeController {
    
    /**
     * Mostrar página principal
     */
    public function index() {
        $pageTitle = 'Inicio - ' . APP_NAME;
        
        // Obtener estadísticas básicas (futuras implementaciones)
        $stats = [
            'total_propiedades' => 0,
            'propiedades_activas' => 0,
            'total_agentes' => 0,
            'total_clientes' => 0
        ];
        
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