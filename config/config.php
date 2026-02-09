<?php

/**
 * Configuración General del Sistema
 * Sistema de Gestión RIAAC
 */

// Zona horaria
date_default_timezone_set('America/Lima');

// URL base del proyecto
define('BASE_URL', 'http://localhost/inventario_riaac/');

// Rutas del sistema (solo si no está ya definido)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de errores (cambiar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de subida de archivos
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'bmp',
    'webp',  // Imágenes
    'pdf',                                        // PDF
    'doc',
    'docx',                               // Word
    'xls',
    'xlsx',                               // Excel
    'ppt',
    'pptx',                               // PowerPoint
    'zip',
    'rar',
    '7z',
    'tar',
    'gz'             // Comprimidos
]);
