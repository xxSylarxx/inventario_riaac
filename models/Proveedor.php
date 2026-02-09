<?php
class Proveedor
{
    private $pdo;
    private $table = 'proveedores';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Obtener todos los proveedores
     */
    public function obtenerTodos()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener proveedor por ID
     */
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Crear nuevo proveedor
     */
    public function crear($datos)
    {
        $sql = "INSERT INTO {$this->table} 
                (nombre, contacto, whatsapp, email, direccion, estado) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['contacto'],
            $datos['whatsapp'],
            $datos['email'],
            $datos['direccion'],
            $datos['estado']
        ]);
    }

    /**
     * Actualizar proveedor
     */
    public function actualizar($id, $datos)
    {
        $sql = "UPDATE {$this->table} 
                SET nombre = ?, contacto = ?, whatsapp = ?, 
                    email = ?, direccion = ?, estado = ?
                WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['contacto'],
            $datos['whatsapp'],
            $datos['email'],
            $datos['direccion'],
            $datos['estado'],
            $id
        ]);
    }

    /**
     * Eliminar proveedor (soft delete - cambiar estado)
     */
    public function eliminar($id)
    {
        $sql = "UPDATE {$this->table} SET estado = 'inactivo' WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Obtener proveedores activos
     */
    public function obtenerActivos()
    {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 'activo' ORDER BY nombre ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
}
