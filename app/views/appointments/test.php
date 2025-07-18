<?php
// Archivo de prueba simple para diagnosticar el problema
?>
<div style="background: #ff6b6b; color: white; padding: 20px; margin: 20px; border-radius: 8px;">
    <h1>PRUEBA DE VISTA DE CITAS</h1>
    <p><strong>Fecha:</strong> <?= date('Y-m-d H:i:s') ?></p>
    <p><strong>pageTitle:</strong> <?= $pageTitle ?? 'No definido' ?></p>
    <p><strong>APP_PATH:</strong> <?= APP_PATH ?? 'No definido' ?></p>
    <p><strong>Usuario autenticado:</strong> <?= isAuthenticated() ? 'Sí' : 'No' ?></p>
    <?php if (isAuthenticated()): ?>
        <p><strong>Usuario:</strong> <?= $_SESSION['user']['nombre'] ?? 'No disponible' ?></p>
        <p><strong>Rol:</strong> <?= $_SESSION['user']['rol'] ?? 'No disponible' ?></p>
    <?php endif; ?>
    <p><strong>Variables disponibles:</strong></p>
    <ul>
        <?php foreach (get_defined_vars() as $var => $value): ?>
            <li><?= $var ?>: <?= is_array($value) ? 'Array(' . count($value) . ')' : $value ?></li>
        <?php endforeach; ?>
    </ul>
</div> 