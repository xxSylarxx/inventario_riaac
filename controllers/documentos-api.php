<?php

/**
 * API para gestión de documentos
 * Endpoints AJAX para subir, listar y eliminar documentos
 */

// Desactivar output buffering y mostrar errores solo en desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en output
ini_set('log_errors', 1);

// Establecer header JSON ANTES de cualquier output
header('Content-Type: application/json; charset=utf-8');

try {
    // Verificar que ROOT_PATH esté definido
    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(dirname(__FILE__)) . '/');
    }

    require_once ROOT_PATH . 'config/config.php';
    require_once ROOT_PATH . 'includes/autoload.php';
    require_once ROOT_PATH . 'includes/helpers.php';
    require_once ROOT_PATH . 'includes/auth.php';

    // Verificar que la clase existe
    if (!class_exists('DocumentoServicio')) {
        throw new Exception('Clase DocumentoServicio no encontrada');
    }

    $documentoModel = new DocumentoServicio($pdo);
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'listar':
            $tipoServicio = $_GET['tipo_servicio'] ?? '';
            $servicioId = (int)($_GET['servicio_id'] ?? 0);

            if (empty($tipoServicio) || $servicioId === 0) {
                throw new Exception('Parámetros inválidos');
            }

            $documentos = $documentoModel->obtenerPorServicio($tipoServicio, $servicioId);
            echo json_encode([
                'success' => true,
                'documentos' => $documentos ?: []
            ]);
            break;

        case 'subir':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $tipoServicio = $_POST['tipo_servicio'] ?? '';
            $servicioId = (int)($_POST['servicio_id'] ?? 0);
            $tipoDocumento = $_POST['tipo_documento'] ?? 'otro';
            $descripcion = $_POST['descripcion'] ?? '';

            if (empty($tipoServicio) || $servicioId === 0) {
                throw new Exception('Datos incompletos');
            }

            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
                throw new Exception('No se seleccionó ningún archivo');
            }

            // Subir archivo
            $carpeta = 'documentos/' . $tipoDocumento;
            $resultado = subirArchivo($_FILES['archivo'], $carpeta);

            if (!$resultado['success']) {
                throw new Exception($resultado['error']);
            }

            // Guardar en base de datos
            $datos = [
                'tipo_servicio' => $tipoServicio,
                'servicio_id' => $servicioId,
                'tipo_documento' => $tipoDocumento,
                'nombre_original' => $_FILES['archivo']['name'],
                'ruta_archivo' => $resultado['ruta'],
                'descripcion' => $descripcion
            ];

            if ($documentoModel->crear($datos)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Documento subido exitosamente'
                ]);
            } else {
                throw new Exception('Error al guardar en base de datos');
            }
            break;

        case 'eliminar':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $id = (int)($_POST['id'] ?? 0);

            if ($id === 0) {
                throw new Exception('ID inválido');
            }

            // Obtener documento para eliminar archivo físico
            $documento = $documentoModel->obtenerPorId($id);

            if (!$documento) {
                throw new Exception('Documento no encontrado');
            }

            // Eliminar archivo físico
            eliminarArchivo($documento['ruta_archivo']);

            // Eliminar de base de datos
            if ($documentoModel->eliminar($id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Documento eliminado exitosamente'
                ]);
            } else {
                throw new Exception('Error al eliminar de base de datos');
            }
            break;

        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
