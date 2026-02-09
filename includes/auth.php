<?php

/**
 * Middleware de Autenticación
 * Protege rutas que requieren autenticación
 */

// Verificar si hay sesión activa
if (!isAuthenticated()) {
    redirect('views/auth/login.php');
}
