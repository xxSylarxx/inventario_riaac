<?php

/**
 * Modelo Cliente
 * Gestión de clientes del negocio
 */

class Cliente
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todos los clientes
     */
    public function obtenerTodos($estado = null)
    {
        $sql = "SELECT * FROM clientes";
        if ($estado) {
            $sql .= " WHERE estado = ?";
        }
        $sql .= " ORDER BY fecha_registro DESC";

        $stmt = $this->pdo->prepare($sql);
        if ($estado) {
            $stmt->execute([$estado]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Obtener clientes activos (para selectores)
     */
    public function obtenerActivos()
    {
        return $this->obtenerTodos('activo');
    }

    /**
     * Obtener cliente por ID
     */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Buscar clientes
     */
    public function buscar($termino)
    {
        $stmt = $this->pdo->prepare("
SELECT * FROM clientes
WHERE nombre LIKE ? OR dni_ruc LIKE ? OR email LIKE ?
ORDER BY nombre ASC
");
        $termino = "%$termino%";
        $stmt->execute([$termino, $termino, $termino]);
        return $stmt->fetchAll();
    }

    /**
     * Crear cliente
     */
    public function crear($datos)
    {
        $stmt = $this->pdo->prepare("
INSERT INTO clientes (tipo, nombre, dni_ruc, email, telefono, whatsapp, direccion, ubicacion_google_maps, estado)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

        return $stmt->execute([
            $datos['tipo'],
            $datos['nombre'],
            $datos['dni_ruc'] ?? null,
            $datos['email'] ?? null,
            $datos['telefono'] ?? null,
            $datos['whatsapp'] ?? null,
            $datos['direccion'] ?? null,
            $datos['ubicacion_google_maps'] ?? null,
            $datos['estado'] ?? 'activo'
        ]);
    }

    /**
     * Actualizar cliente
     */
    public function actualizar($id, $datos)
    {
        $stmt = $this->pdo->prepare("
UPDATE clientes
SET tipo = ?, nombre = ?, dni_ruc = ?, email = ?, telefono = ?,
whatsapp = ?, direccion = ?, ubicacion_google_maps = ?, estado = ?
WHERE id = ?
");

        return $stmt->execute([
            $datos['tipo'],
            $datos['nombre'],
            $datos['dni_ruc'] ?? null,
            $datos['email'] ?? null,
            $datos['telefono'] ?? null,
            $datos['whatsapp'] ?? null,
            $datos['direccion'] ?? null,
            $datos['ubicacion_google_maps'] ?? null,
            $datos['estado'],
            $id
        ]);
    }

    /**
     * Eliminar cliente
     */
    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM clientes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar clientes activos
     */
    public function contarActivos()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM clientes WHERE estado = 'activo'");
        return $stmt->fetchColumn();
    }
}
