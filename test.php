<?php

/**
 * Archivo de Prueba
 * Verifica que PHP esté funcionando correctamente
 */

echo "<h1>✓ PHP está funcionando correctamente</h1>";
echo "<p>Versión de PHP: " . phpversion() . "</p>";

// Probar conexión a MySQL
echo "<h2>Probando conexión a MySQL...</h2>";

try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    echo "<p style='color: green;'>✓ Conexión a MySQL exitosa</p>";

    // Verificar si existe la base de datos
    $stmt = $pdo->query("SHOW DATABASES LIKE 'inventario_riaac'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Base de datos 'inventario_riaac' existe</p>";

        // Verificar tablas
        $pdo->exec("USE inventario_riaac");
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "<p style='color: green;'>✓ Tablas encontradas: " . count($tables) . "</p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";

        echo "<h2>✅ Todo está listo!</h2>";
        echo "<p><a href='http://localhost/inventario_riaac/'>Ir al sistema</a></p>";
    } else {
        echo "<p style='color: red;'>✗ Base de datos 'inventario_riaac' NO existe</p>";
        echo "<h3>Pasos para crear la base de datos:</h3>";
        echo "<ol>";
        echo "<li>Abre phpMyAdmin: <a href='http://localhost/phpmyadmin/' target='_blank'>http://localhost/phpmyadmin/</a></li>";
        echo "<li>Haz clic en 'Nueva' en el panel izquierdo</li>";
        echo "<li>Nombre de la base de datos: <strong>inventario_riaac</strong></li>";
        echo "<li>Cotejamiento: <strong>utf8mb4_unicode_ci</strong></li>";
        echo "<li>Haz clic en 'Crear'</li>";
        echo "<li>Ve a la pestaña 'Importar'</li>";
        echo "<li>Selecciona el archivo: <code>C:\\wamp642025\\www\\inventario_riaac\\database\\database.sql</code></li>";
        echo "<li>Haz clic en 'Continuar'</li>";
        echo "<li>Recarga esta página para verificar</li>";
        echo "</ol>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error al conectar a MySQL: " . $e->getMessage() . "</p>";
    echo "<p>Asegúrate de que WAMP esté corriendo (ícono verde)</p>";
}
