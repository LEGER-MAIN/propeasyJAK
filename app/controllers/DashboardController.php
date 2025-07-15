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
        $pageTitle = 'Dashboard - ' . APP_NAME;
        include APP_PATH . '/views/dashboard/index.php';
    }
} 