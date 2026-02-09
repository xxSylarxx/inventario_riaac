<?php

/**
 * Modelo DocumentoServicio
 * Gestión de documentos adjuntos a servicios
 */

class DocumentoServicio
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todos los documentos de un servicio
     */
    public function obtenerPorServicio($tipoServicio, $servicioId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM documentos
            WHERE tipo_servicio = ? AND servicio_id = ?
            ORDER BY fecha_subida DESC
        ");
        $stmt->execute([$tipoServicio, $servicioId]);
        return $stmt->fetchAll();
    }

    /**
     * Crear nuevo documento
     */
    public function crear($datos)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO documentos
            (tipo_servicio, servicio_id, tipo_documento, nombre_original, ruta_archivo, descripcion)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $datos['tipo_servicio'],
            $datos['servicio_id'],
            $datos['tipo_documento'],
            $datos['nombre_original'],
            $datos['ruta_archivo'],
            $datos['descripcion'] ?? null
        ]);
    }

    /**
     * Obtener documento por ID
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM documentos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Eliminar documento
     */
    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM documentos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar documentos de un servicio
     */
    public function contarPorServicio($tipoServicio, $servicioId)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM documentos
            WHERE tipo_servicio = ? AND servicio_id = ?
        ");
        $stmt->execute([$tipoServicio, $servicioId]);
        return $stmt->fetchColumn();
    }

    /**
     * Obtener documentos por tipo
     */
    public function obtenerPorTipo($tipoServicio, $servicioId, $tipoDocumento)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM documentos
            WHERE tipo_servicio = ? AND servicio_id = ? AND tipo_documento = ?
            ORDER BY fecha_subida DESC
        ");
        $stmt->execute([$tipoServicio, $servicioId, $tipoDocumento]);
        return $stmt->fetchAll();
    }
}
