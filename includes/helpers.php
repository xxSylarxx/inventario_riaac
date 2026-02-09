<?php

/**
 * Funciones de Ayuda (Helpers)
 * Sistema de Gestión RIAAC
 */

/**
 * Sanitizar entrada de datos
 */
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear fecha en español
 */
function formatearFecha($fecha)
{
    if (!$fecha) return '-';
    $timestamp = strtotime($fecha);
    return date('d/m/Y', $timestamp);
}

/**
 * Formatear fecha y hora en español
 */
function formatearFechaHora($fecha)
{
    if (!$fecha) return '-';
    $timestamp = strtotime($fecha);
    return date('d/m/Y H:i', $timestamp);
}

/**
 * Formatear moneda
 */
function formatearMoneda($monto)
{
    return 'S/ ' . number_format($monto, 2, '.', ',');
}

/**
 * Redireccionar
 */
function redirect($url)
{
    header("Location: " . BASE_URL . $url);
    exit();
}

/**
 * Verificar si el usuario está autenticado
 */
function isAuthenticated()
{
    return isset($_SESSION['usuario_id']);
}

/**
 * Verificar si el usuario es administrador
 */
function isAdmin()
{
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador';
}

/**
 * Obtener usuario actual
 */
function getUsuarioActual()
{
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nombre' => $_SESSION['nombre'] ?? null,
        'usuario' => $_SESSION['usuario'] ?? null,
        'rol' => $_SESSION['rol'] ?? null,
    ];
}

/**
 * Calcular días entre fechas
 */
function diasEntreFechas($fecha1, $fecha2)
{
    $datetime1 = new DateTime($fecha1);
    $datetime2 = new DateTime($fecha2);
    $interval = $datetime1->diff($datetime2);
    return $interval->days;
}

/**
 * Validar formato de email
 */
function validarEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generar nombre único para archivo
 */
function generarNombreUnico($extension)
{
    return uniqid('file_', true) . '.' . $extension;
}

/**
 * Validar extensión de archivo
 */
function validarExtension($filename)
{
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, ALLOWED_EXTENSIONS);
}

/**
 * Subir archivo de presupuesto
 * @param array $file - Archivo de $_FILES
 * @param string $carpeta - Carpeta destino (presupuestos o documentos/tipo)
 * @return array - ['success' => bool, 'ruta' => string, 'error' => string]
 */
function subirArchivo($file, $carpeta = 'presupuestos')
{
    // Validar que se haya subido un archivo
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'ruta' => null, 'error' => 'No se seleccionó ningún archivo'];
    }

    // Validar errores de subida
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'ruta' => null, 'error' => 'Error al subir el archivo'];
    }

    // Validar tamaño
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'ruta' => null, 'error' => 'El archivo es demasiado grande (máx 5MB)'];
    }

    // Validar extensión
    if (!validarExtension($file['name'])) {
        return ['success' => false, 'ruta' => null, 'error' => 'Formato de archivo no permitido. Formatos aceptados: PDF, imágenes (JPG, PNG, GIF), Office (Word, Excel, PowerPoint), archivos comprimidos (ZIP, RAR)'];
    }

    // Crear carpeta si no existe (incluye subcarpetas)
    $directorioBase = UPLOADS_PATH . $carpeta . '/';
    if (!file_exists($directorioBase)) {
        mkdir($directorioBase, 0755, true);
    }

    // Generar nombre único
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $nombreArchivo = uniqid('doc_' . date('Ymd') . '_', true) . '.' . $extension;
    $rutaCompleta = $directorioBase . $nombreArchivo;

    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
        // Retornar ruta relativa para guardar en BD
        $rutaRelativa = 'uploads/' . $carpeta . '/' . $nombreArchivo;
        return ['success' => true, 'ruta' => $rutaRelativa, 'error' => null];
    } else {
        return ['success' => false, 'ruta' => null, 'error' => 'Error al mover el archivo al servidor'];
    }
}

/**
 * Eliminar archivo de presupuesto
 * @param string $ruta - Ruta relativa del archivo
 * @return bool
 */
function eliminarArchivo($ruta)
{
    if (empty($ruta)) {
        return false;
    }

    $rutaCompleta = ROOT_PATH . $ruta;
    if (file_exists($rutaCompleta)) {
        return unlink($rutaCompleta);
    }

    return false;
}


/**
 * Obtener badge de estado de garantía
 */
function getBadgeGarantia($estado)
{
    $badges = [
        'vigente' => '<span class="badge bg-success">Vigente</span>',
        'vencida' => '<span class="badge bg-danger">Vencida</span>',
        'sin_entrega' => '<span class="badge bg-secondary">Sin entregar</span>',
    ];
    return $badges[$estado] ?? '<span class="badge bg-secondary">N/A</span>';
}

/**
 * Obtener badge de alerta de vencimiento
 */
function getBadgeAlerta($dias)
{
    if ($dias <= 5) {
        return '<span class="badge bg-danger">Vence en ' . $dias . ' días</span>';
    } elseif ($dias <= 15) {
        return '<span class="badge bg-warning">Vence en ' . $dias . ' días</span>';
    } elseif ($dias <= 30) {
        return '<span class="badge bg-info">Vence en ' . $dias . ' días</span>';
    }
    return '';
}

/**
 * Mostrar mensaje de sesión
 */
function mostrarMensaje()
{
    if (isset($_SESSION['mensaje'])) {
        $tipo = $_SESSION['tipo_mensaje'] ?? 'info';
        $mensaje = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        unset($_SESSION['tipo_mensaje']);

        echo '<div class="alert alert-' . $tipo . ' alert-dismissible fade show" role="alert">';
        echo $mensaje;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

/**
 * Establecer mensaje de sesión
 */
function setMensaje($mensaje, $tipo = 'success')
{
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = $tipo;
}
