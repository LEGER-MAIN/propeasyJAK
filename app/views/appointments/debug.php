<?php
// Archivo de debug para verificar el estado del sistema
?>
<div style="background: #96ceb4; color: white; padding: 20px; margin: 20px; border-radius: 8px;">
    <h1>DEBUG DEL SISTEMA</h1>
    <p><strong>Fecha:</strong> <?= date('Y-m-d H:i:s') ?></p>
    <p><strong>pageTitle:</strong> <?= $pageTitle ?? 'No definido' ?></p>
    <p><strong>APP_PATH:</strong> <?= APP_PATH ?? 'No definido' ?></p>
    <p><strong>APP_NAME:</strong> <?= APP_NAME ?? 'No definido' ?></p>
    <p><strong>Usuario autenticado:</strong> <?= isAuthenticated() ? 'Sí' : 'No' ?></p>
    
    <h2>Información de Sesión:</h2>
    <pre style="background: rgba(0,0,0,0.1); padding: 10px; border-radius: 4px; overflow-x: auto;">
<?= print_r($_SESSION ?? [], true) ?>
    </pre>
    
    <h2>Información del Servidor:</h2>
    <ul>
        <li><strong>REQUEST_URI:</strong> <?= $_SERVER['REQUEST_URI'] ?? 'No disponible' ?></li>
        <li><strong>REQUEST_METHOD:</strong> <?= $_SERVER['REQUEST_METHOD'] ?? 'No disponible' ?></li>
        <li><strong>SCRIPT_NAME:</strong> <?= $_SERVER['SCRIPT_NAME'] ?? 'No disponible' ?></li>
        <li><strong>PHP_VERSION:</strong> <?= PHP_VERSION ?></li>
    </ul>
    
    <h2>Variables Definidas:</h2>
    <ul>
        <?php foreach (get_defined_vars() as $var => $value): ?>
            <li><?= $var ?>: <?= is_array($value) ? 'Array(' . count($value) . ')' : $value ?></li>
        <?php endforeach; ?>
    </ul>
    
    <h2>Funciones Disponibles:</h2>
    <ul>
        <li><strong>isAuthenticated():</strong> <?= function_exists('isAuthenticated') ? 'Sí' : 'No' ?></li>
        <li><strong>hasRole():</strong> <?= function_exists('hasRole') ? 'Sí' : 'No' ?></li>
        <li><strong>redirect():</strong> <?= function_exists('redirect') ? 'Sí' : 'No' ?></li>
        <li><strong>getFlashMessages():</strong> <?= function_exists('getFlashMessages') ? 'Sí' : 'No' ?></li>
    </ul>
</div> 