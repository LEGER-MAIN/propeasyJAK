<?php
/**
 * Controlador DashboardController - Panel principal de usuario
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

class DashboardController {
    /**
     * Mostrar el dashboard del usuario
     */
    public function index() {
        // Verificar que el usuario esté autenticado
        requireAuth();
        
        // Si el usuario es administrador, redirigir al panel administrativo
        if (hasRole(ROLE_ADMIN)) {
            redirect('/admin/dashboard');
        }
        
        // Si el usuario es agente, redirigir al dashboard de agente
        if (hasRole(ROLE_AGENTE)) {
            redirect('/agente/dashboard');
        }
        
        // Si el usuario es cliente, redirigir al dashboard de cliente
        if (hasRole(ROLE_CLIENTE)) {
            redirect('/cliente/dashboard');
        }
        
        // Fallback: mostrar dashboard general
        $pageTitle = 'Dashboard - ' . APP_NAME;
        include APP_PATH . '/views/dashboard/index.php';
    }
} 
