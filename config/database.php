<?php

/**
 * Configuración de Base de Datos
 * Sistema de Gestión RIAAC
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventario_riaac');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Crear conexión PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    echo "<h1>Error de Conexión a la Base de Datos</h1>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<h3>Pasos para solucionar:</h3>";
    echo "<ol>";
    echo "<li>Abre phpMyAdmin: <a href='http://localhost/phpmyadmin/'>http://localhost/phpmyadmin/</a></li>";
    echo "<li>Crea una base de datos llamada: <strong>inventario_riaac</strong></li>";
    echo "<li>Importa el archivo: <code>C:\\wamp642025\\www\\inventario_riaac\\database\\database.sql</code></li>";
    echo "<li>Recarga esta página</li>";
    echo "</ol>";
    exit();
}
