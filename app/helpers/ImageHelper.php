<?php
/**
 * Helper para manejo de imágenes
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

/**
 * Obtener URL completa de una imagen
 * 
 * @param string $ruta Ruta relativa de la imagen
 * @return string URL completa de la imagen
 */
function getImageUrl($ruta) {
    if (empty($ruta)) {
        return '';
    }
    
    // Si ya es una URL completa, retornarla tal como está
    if (strpos($ruta, 'http') === 0) {
        return $ruta;
    }
    
    // Si empieza con /uploads, construir la URL completa
    if (strpos($ruta, '/uploads') === 0) {
        $baseUrl = function_exists('getDynamicBaseUrl') ? getDynamicBaseUrl() : APP_URL;
        return $baseUrl . $ruta;
    }
    
    // Si es solo el nombre del archivo, construir la ruta completa
    if (strpos($ruta, '/') === false) {
        $baseUrl = function_exists('getDynamicBaseUrl') ? getDynamicBaseUrl() : APP_URL;
        return $baseUrl . '/uploads/properties/' . $ruta;
    }
    
    // Para otros casos, usar la ruta tal como está
    return $ruta;
}

/**
 * Obtener URL de imagen de propiedad desde la base de datos
 * 
 * @param string $ruta Ruta almacenada en la base de datos
 * @return string URL completa de la imagen
 */
function getPropertyImageUrl($ruta) {
    if (empty($ruta)) {
        return '';
    }
    
    // Si la ruta ya incluye /uploads/properties/, construir URL completa
    if (strpos($ruta, '/uploads/properties/') === 0) {
        $baseUrl = function_exists('getDynamicBaseUrl') ? getDynamicBaseUrl() : APP_URL;
        return $baseUrl . $ruta;
    }
    
    // Si es solo el nombre del archivo
    if (strpos($ruta, '/') === false) {
        $baseUrl = function_exists('getDynamicBaseUrl') ? getDynamicBaseUrl() : APP_URL;
        return $baseUrl . '/uploads/properties/' . $ruta;
    }
    
    // Para otros casos, usar getImageUrl
    return getImageUrl($ruta);
}

/**
 * Obtener URL de imagen de perfil
 * 
 * @param string $ruta Ruta de la imagen de perfil
 * @return string URL completa de la imagen
 */
function getProfileImageUrl($ruta) {
    if (empty($ruta)) {
        return '';
    }
    
    // Si la ruta ya incluye /uploads/profiles/, construir URL completa
    if (strpos($ruta, '/uploads/profiles/') === 0) {
        $baseUrl = function_exists('getAppUrl') ? getAppUrl() : APP_URL;
        return $baseUrl . $ruta;
    }
    
    // Si es solo el nombre del archivo
    if (strpos($ruta, '/') === false) {
        $baseUrl = function_exists('getAppUrl') ? getAppUrl() : APP_URL;
        return $baseUrl . '/uploads/profiles/' . $ruta;
    }
    
    // Para otros casos, usar getImageUrl
    return getImageUrl($ruta);
}

/**
 * Verificar si una imagen existe
 * 
 * @param string $ruta Ruta de la imagen
 * @return bool True si la imagen existe
 */
function imageExists($ruta) {
    if (empty($ruta)) {
        return false;
    }
    
    // Convertir URL a ruta del sistema de archivos
    $filePath = '';
    
    if (strpos($ruta, '/uploads/properties/') === 0) {
        $filePath = PUBLIC_PATH . $ruta;
    } elseif (strpos($ruta, '/uploads/profiles/') === 0) {
        $filePath = PUBLIC_PATH . $ruta;
    } elseif (strpos($ruta, '/') === false) {
        $filePath = PUBLIC_PATH . '/uploads/properties/' . $ruta;
    } else {
        $filePath = PUBLIC_PATH . '/uploads/' . $ruta;
    }
    
    return file_exists($filePath);
}

/**
 * Obtener imagen por defecto para propiedades
 * 
 * @return string URL de la imagen por defecto
 */
function getDefaultPropertyImage() {
    $baseUrl = function_exists('getAppUrl') ? getAppUrl() : APP_URL;
    return $baseUrl . '/uploads/properties/default-property.jpg';
}

/**
 * Obtener imagen por defecto para perfiles
 * 
 * @return string URL de la imagen por defecto
 */
function getDefaultProfileImage() {
    $baseUrl = function_exists('getAppUrl') ? getAppUrl() : APP_URL;
    return $baseUrl . '/uploads/profiles/default-profile.jpg';
} 