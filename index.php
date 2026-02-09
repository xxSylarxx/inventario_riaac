<?php

/**
 * Archivo de Entrada Principal
 * Sistema de Gestión RIAAC
 */

define('ROOT_PATH', __DIR__ . '/');
require_once ROOT_PATH . 'includes/autoload.php';

// Redirigir según estado de autenticación
if (isAuthenticated()) {
    header('Location: views/dashboard/index.php');
} else {
    header('Location: views/auth/login.php');
}
exit();
