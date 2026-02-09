<?php

/**
 * Autoload de clases
 * Carga automáticamente modelos y controladores
 */

spl_autoload_register(function ($class) {
    // Buscar en carpeta models
    $modelFile = ROOT_PATH . 'models/' . $class . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }

    // Buscar en carpeta controllers
    $controllerFile = ROOT_PATH . 'controllers/' . $class . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return;
    }
});

// Cargar configuraciones
require_once ROOT_PATH . 'config/config.php';
require_once ROOT_PATH . 'config/database.php';
require_once ROOT_PATH . 'includes/helpers.php';
