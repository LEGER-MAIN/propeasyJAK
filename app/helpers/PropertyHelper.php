<?php
/**
 * Helper para funciones relacionadas con propiedades
 * PropEasy - Sistema Web de Venta de Bienes Raíces
 */

/**
 * Convertir tipos de propiedad de snake_case a formato legible
 * 
 * @param string $type Tipo de propiedad en snake_case
 * @return string Tipo de propiedad en formato legible
 */
function getPropertyTypeDisplayName($type) {
    $typeMap = [
        'casa' => 'Casa',
        'apartamento' => 'Apartamento',
        'terreno' => 'Terreno',
        'comercial' => 'Local Comercial',
        'local_comercial' => 'Local Comercial',
        'oficina' => 'Oficina',
        'bodega' => 'Bodega',
        'estacionamiento' => 'Estacionamiento'
    ];
    
    return $typeMap[$type] ?? ucfirst(str_replace('_', ' ', $type));
}

/**
 * Obtener tipos únicos de propiedades para filtros
 * 
 * @return array Array asociativo de tipos de propiedades
 */
function getUniquePropertyTypes() {
    return [
        'casa' => 'Casa',
        'apartamento' => 'Apartamento',
        'terreno' => 'Terreno',
        'local_comercial' => 'Local Comercial',
        'oficina' => 'Oficina',
        'bodega' => 'Bodega',
        'estacionamiento' => 'Estacionamiento'
    ];
} 