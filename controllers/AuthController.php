<?php

/**
 * Controlador de Autenticación
 * Maneja login, logout y protección de rutas
 */

define('ROOT_PATH', dirname(__DIR__) . '/');
require_once ROOT_PATH . 'includes/autoload.php';

// Obtener acción
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        login();
        break;

    case 'logout':
        logout();
        break;

    default:
        redirect('views/auth/login.php');
}

/**
 * Procesar login
 */
function login()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('views/auth/login.php');
        return;
    }

    $usuario = sanitize($_POST['usuario']);
    $password = $_POST['password'];

    $usuarioModel = new Usuario($GLOBALS['pdo']);

    if ($usuarioModel->autenticar($usuario, $password)) {
        redirect('views/dashboard/index.php');
    } else {
        $_SESSION['error_login'] = 'Usuario o contraseña incorrectos';
        redirect('views/auth/login.php');
    }
}

/**
 * Cerrar sesión
 */
function logout()
{
    $usuarioModel = new Usuario($GLOBALS['pdo']);
    $usuarioModel->cerrarSesion();
    redirect('views/auth/login.php');
}
